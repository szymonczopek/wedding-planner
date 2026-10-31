<script setup>
import {computed, reactive, ref, watch} from 'vue'
import AutoComplete from 'primevue/autocomplete'
import Button from 'primevue/button'
import Checkbox from 'primevue/checkbox'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import RadioButton from 'primevue/radiobutton'
import Select from 'primevue/select'
import FormField from './FormField.vue'
import api from '../../api/client.js'
import {getValidationErrors} from '../../api/errors.js'
import {acquaintanceTypes, diets} from '../../constants/guests.js'
import {useAuth} from '../../composables/useAuth.js'
import {useGuest} from '../../composables/useGuest.js'

const props = defineProps({
  visible: {type: Boolean, default: false},
})

const emit = defineEmits(['update:visible', 'created'])

const {user} = useAuth()
const {searchAddresses, searchPolishAddresses, fetchGuestsList} = useGuest()

const isSubmitting = ref(false)
const errors = ref({})
const addressMode = ref('existing')
const selectedAddress = ref(null)
const addressSuggestions = ref([])
const selectedPolishAddress = ref(null)
const polishAddressSuggestions = ref([])
const guests = ref([])
const isSearchingAddress = ref(false)
const isSearchingPolishAddress = ref(false)
const addressSearchInputProps = {
  name: 'place-search-query',
  autocomplete: 'one-time-code',
  'data-form-type': 'other',
  'data-lpignore': 'true',
  'data-1p-ignore': 'true',
  spellcheck: 'false',
}
let lastAddressQuery = ''
let lastPolishAddressQuery = ''

const selectedWedding = computed(() => {
  const weddings = user.value?.weddings ?? []

  return weddings.find((wedding) => wedding.pivot?.selected) ?? weddings[0] ?? null
})

const selectedAddressId = computed(() => selectedAddress.value?.id ?? null)

const isGroom = computed(() => selectedWedding.value?.pivot?.role === 'groom')

function emptyForm() {
  return {
    name: '',
    surname: '',
    acquaintance_type: null,
    child: false,
    guest_side: isGroom.value ? 'groom' : 'bride',
    diet: diets[0]?.value ?? null,
    main_guest_id: null,
    address: {
      street: '',
      house_number: '',
      local_number: '',
      city: '',
      district: '',
      postal_code: '',
      country: 'Polska',
    },
  }
}

const form = reactive(emptyForm())

const guestOptions = computed(() =>
    guests.value.map((guest) => ({
      label: `${guest.name} ${guest.surname}`,
      value: guest.id,
    })),
)

function resetForm() {
  Object.assign(form, emptyForm())
  errors.value = {}
  isSubmitting.value = false
  addressMode.value = 'existing'
  selectedAddress.value = null
  addressSuggestions.value = []
  selectedPolishAddress.value = null
  polishAddressSuggestions.value = []
  isSearchingAddress.value = false
  isSearchingPolishAddress.value = false
  lastAddressQuery = ''
  lastPolishAddressQuery = ''
}

function formatAddress(address) {
  const street = [address.street, address.house_number].filter(Boolean).join(' ')
  const localNumber = address.local_number ? `/${address.local_number}` : ''
  const city = [address.postal_code, address.city].filter(Boolean).join(' ')

  if (!address.street) {
    return [
      [address.city, address.house_number].filter(Boolean).join(' '),
      address.postal_code ? city : null,
      address.country,
    ].filter(Boolean).join(', ')
  }

  return [`${street}${localNumber}`, city, address.country].filter(Boolean).join(', ')
}

async function searchAddress(event) {
  const query = (event?.query ?? '').trim()
  lastAddressQuery = query

  if (query.length < 2) {
    addressSuggestions.value = []
    isSearchingAddress.value = false
    return
  }

  isSearchingAddress.value = true

  try {
    const data = await searchAddresses(query)

    if (lastAddressQuery !== query) {
      return
    }

    addressSuggestions.value = data.map((address) => ({
      ...address,
      label: formatAddress(address),
    }))
  } catch {
    if (lastAddressQuery === query) {
      addressSuggestions.value = []
    }
  } finally {
    if (lastAddressQuery === query) {
      isSearchingAddress.value = false
    }
  }
}

async function searchPolishAddress(event) {
  const query = (event?.query ?? '').trim()
  lastPolishAddressQuery = query

  if (query.length < 2) {
    polishAddressSuggestions.value = []
    isSearchingPolishAddress.value = false
    return
  }

  isSearchingPolishAddress.value = true

  try {
    const data = await searchPolishAddresses(query)

    if (lastPolishAddressQuery !== query) {
      return
    }

    polishAddressSuggestions.value = data
  } catch {
    if (lastPolishAddressQuery === query) {
      polishAddressSuggestions.value = []
    }
  } finally {
    if (lastPolishAddressQuery === query) {
      isSearchingPolishAddress.value = false
    }
  }
}

function selectPolishAddress(event) {
  const address = event?.value

  if (!address) {
    return
  }

  form.address.street = address.street ?? ''
  form.address.city = address.city ?? ''
  form.address.district = address.district ?? ''
  form.address.postal_code = address.postal_code ?? ''
  form.address.country = address.country ?? 'Polska'

  delete errors.value['address.street']
  delete errors.value['address.city']
  delete errors.value['address.district']
  delete errors.value['address.postal_code']
  delete errors.value['address.country']
}

function clearPolishAddress() {
  form.address.street = ''
  form.address.city = ''
  form.address.district = ''
  form.address.postal_code = ''
  form.address.country = 'Polska'
}

async function loadGuests() {
  try {
    guests.value = await fetchGuestsList()
  } catch {
    guests.value = []
  }
}

async function submitGuest() {
  errors.value = {}

  if (addressMode.value === 'existing' && !selectedAddressId.value) {
    errors.value = {address_id: ['Wybierz adres z wyszukiwarki']}
    return
  }

  isSubmitting.value = true

  try {
    const payload = {
      name: form.name,
      surname: form.surname,
      acquaintance_type: form.acquaintance_type,
      child: form.child,
      groom_guest:
        form.guest_side === 'groom'
          ? true
          : form.guest_side === 'bride'
            ? false
            : null,
      diet: form.diet
    }

    if (form.main_guest_id) {
      payload.main_guest_id = form.main_guest_id
    }

    if (addressMode.value === 'existing') {
      payload.address_id = selectedAddressId.value
    } else {
      payload.address = {
        street: form.address.street || null,
        house_number: form.address.house_number,
        local_number: form.address.local_number || null,
        city: form.address.city,
        district: form.address.district || null,
        postal_code: form.address.postal_code || null,
        country: form.address.country || null,
      }
    }

    await api.post('/guests/create', payload)
    emit('created')
    emit('update:visible', false)
    await loadGuests()
  } catch (e) {
    errors.value = getValidationErrors(e)
  } finally {
    isSubmitting.value = false
  }
}

function closeModal() {
  emit('update:visible', false)
}

watch(
    () => props.visible,
    async (visible) => {
      if (!visible) {
        return
      }

      resetForm()
      await loadGuests()
    },
)
</script>

<template>
  <Dialog
      :visible="visible"
      header="Dodaj gościa"
      modal
      :draggable="true"
      :style="{ width: '42rem' }"
      :breakpoints="{ '640px': '95vw' }"
      @update:visible="emit('update:visible', $event)"
  >
    <form id="add-guest-form" class="guest-form" autocomplete="off" @submit.prevent="submitGuest">
      1. Dane
      <div class="form-grid">
        <FormField id="guest-name" label="Imię" :error="errors.name?.[0]">
          <template #default="{ id, invalid }">
            <InputText :id="id" v-model="form.name" type="text" :invalid="invalid" fluid/>
          </template>
        </FormField>
        <FormField id="guest-surname" label="Nazwisko" :error="errors.surname?.[0]">
          <template #default="{ id, invalid }">
            <InputText :id="id" v-model="form.surname" type="text" :invalid="invalid" fluid/>
          </template>
        </FormField>
        <FormField id="guest-acquaintance-type" label="Rodzaj znajomości" :error="errors.acquaintance_type?.[0]">
          <template #default="{ id, invalid }">
            <Select
                :inputId="id"
                v-model="form.acquaintance_type"
                :options="acquaintanceTypes"
                optionLabel="label"
                optionValue="value"
                :invalid="invalid"
                fluid
            />
          </template>
        </FormField>
        <FormField id="guest-diet" label="Dieta" :error="errors.diet?.[0]">
          <template #default="{ id, invalid }">
            <Select
                :inputId="id"
                v-model="form.diet"
                :options="diets"
                optionLabel="label"
                optionValue="value"
                :invalid="invalid"
                fluid
            />
          </template>
        </FormField>
        <div>
          2. Strona:
          <div class="checkbox-item">
            <RadioButton
                v-model="form.guest_side"
                inputId="guest-groom-side"
                name="groom-guest-side"
                value="groom"
            />
            <label for="guest-groom-side">Gość Pana Młodego</label>
          </div>
          <div class="checkbox-item">
            <RadioButton
                v-model="form.guest_side"
                inputId="guest-bride-side"
                name="groom-guest-side"
                value="bride"
            />
            <label for="guest-bride-side">Gość Panny Młodej</label>
          </div>
          <div class="checkbox-item">
            <RadioButton
                v-model="form.guest_side"
                inputId="guest-both"
                name="groom-guest-side"
                value="both"
            />
            <label for="guest-both">Gość wspólny</label>
          </div>
          3. Wiek
          <div class="checkbox-item">
            <Checkbox v-model="form.child" inputId="guest-child" binary/>
            <label for="guest-child">Dziecko</label>
          </div>
        </div>
        <div class="full-width">
          <FormField id="guest-main-guest" label="Główny gość (opcjonalnie)" :error="errors.main_guest_id?.[0]">
            <template #default="{ id, invalid }">
              <Select
                  :inputId="id"
                  v-model="form.main_guest_id"
                  :options="guestOptions"
                  optionLabel="label"
                  optionValue="value"
                  filter
                  showClear
                  :invalid="invalid"
                  fluid
              />
            </template>
          </FormField>
        </div>
        <div class="address-mode">
          4. Adres:
          <div class="address-mode-option">
            <RadioButton
                v-model="addressMode"
                inputId="address-mode-existing"
                name="address-mode"
                value="existing"
            />
            <label for="address-mode-existing">Dołącz do zapisanego adresu istniejącego gościa</label>
          </div>
          <div class="address-mode-option">
            <RadioButton
                v-model="addressMode"
                inputId="address-mode-new"
                name="address-mode"
                value="new"
            />
            <label for="address-mode-new">Dodaj nowy adres</label>
          </div>
        </div>

        <div v-if="addressMode === 'existing'" class="form-grid">
          <div class="full-width">
            <FormField id="address-search" label="Wyszukiwarka adresu" :error="errors.address_id?.[0]">
              <template #default="{ id, invalid }">
                <AutoComplete
                    :inputId="id"
                    v-model="selectedAddress"
                    :suggestions="addressSuggestions"
                    optionLabel="label"
                    dataKey="id"
                    :delay="300"
                    :minLength="2"
                    :loading="isSearchingAddress"
                    showClear
                    placeholder="Wybierz adres"
                    emptySearchMessage="Brak wyników"
                    :invalid="invalid"
                    fluid
                    @complete="searchAddress"
                />
              </template>
            </FormField>
          </div>
        </div>

        <div v-else class="form-grid">
          <div class="full-width">
            <FormField
                id="place-search-query"
                label="Wyszukaj miejscowość lub ulicę"
            >
              <template #default="{ id, invalid }">
                <AutoComplete
                    :inputId="id"
                    v-model="selectedPolishAddress"
                    :suggestions="polishAddressSuggestions"
                    optionLabel="label"
                    dataKey="id"
                    :delay="300"
                    :minLength="2"
                    :loading="isSearchingPolishAddress"
                    :inputProps="addressSearchInputProps"
                    showClear
                    placeholder="np. Warszawa Puławska"
                    emptySearchMessage="Brak wyników"
                    :invalid="invalid"
                    fluid
                    @complete="searchPolishAddress"
                    @option-select="selectPolishAddress"
                    @clear="clearPolishAddress"
                />
              </template>
            </FormField>
          </div>
          <FormField id="address-city" label="Miejscowość" :error="errors['address.city']?.[0]">
            <template #default="{ id, invalid }">
              <InputText
                  :id="id"
                  v-model="form.address.city"
                  type="text"
                  autocomplete="off"
                  :invalid="invalid"
                  fluid
              />
            </template>
          </FormField>
          <FormField id="address-street" label="Ulica" :error="errors['address.street']?.[0]">
            <template #default="{ id, invalid }">
              <InputText
                  :id="id"
                  v-model="form.address.street"
                  type="text"
                  autocomplete="off"
                  :invalid="invalid"
                  fluid
              />
            </template>
          </FormField>
          <div class="full-width">
            <FormField id="address-district" label="Powiat" :error="errors['address.district']?.[0]">
              <template #default="{ id, invalid }">
                <InputText
                    :id="id"
                    v-model="form.address.district"
                    type="text"
                    autocomplete="off"
                    :invalid="invalid"
                    fluid
                />
              </template>
            </FormField>
          </div>
          <FormField id="address-house-number" label="Numer domu" :error="errors['address.house_number']?.[0]">
            <template #default="{ id, invalid }">
              <InputText
                  :id="id"
                  v-model="form.address.house_number"
                  type="text"
                  autocomplete="off"
                  :invalid="invalid"
                  fluid
              />
            </template>
          </FormField>
          <FormField id="address-local-number" label="Numer lokalu" :error="errors['address.local_number']?.[0]">
            <template #default="{ id, invalid }">
              <InputText
                  :id="id"
                  v-model="form.address.local_number"
                  type="text"
                  autocomplete="off"
                  :invalid="invalid"
                  fluid
              />
            </template>
          </FormField>
        </div>
      </div>

      <div class="modal-actions">
        <Button type="button" label="Anuluj" severity="secondary" text @click="closeModal"/>
        <Button type="submit" label="Dodaj" :loading="isSubmitting"/>
      </div>
    </form>
  </Dialog>
</template>

<style scoped>
.guest-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.full-width {
  grid-column: 1 / -1;
}

.address-mode {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.address-mode-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0.5rem 0;
}


.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

@media (max-width: 640px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
