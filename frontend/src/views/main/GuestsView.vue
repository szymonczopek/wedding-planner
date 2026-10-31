<script setup>
import {computed, onMounted, ref} from 'vue'
import Button from 'primevue/button'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import Tag from 'primevue/tag'
import ToggleSwitch from 'primevue/toggleswitch'
import AddGuestForm from '../../components/forms/AddGuestForm.vue'
import {
  acquaintanceTypes,
  acquaintanceTypeSeverities,
  diets,
  dietSeverities,
  optionLabel,
} from '../../constants/guests.js'
import {useGuest} from '../../composables/useGuest.js'

const {guestsTable} = useGuest()

const isModalOpen = ref(false)
const guests = ref([])
const expandedRowGroups = ref([])
const groupByAddress = ref(true)

function formatAddress(address) {
  const parts = []

  if (address.street) {
    const localNumber = address.local_number ? `/${address.local_number}` : ''

    parts.push(`${[address.street, address.house_number].filter(Boolean).join(' ')}${localNumber}`)
    parts.push([address.postal_code, address.city].filter(Boolean).join(' '))
  } else {
    parts.push([address.city, address.house_number].filter(Boolean).join(' '))
    parts.push(address.postal_code)
  }

  parts.push(address.district, address.country)

  return parts.filter(Boolean).join(', ')
}

const guestRows = computed(() =>
  guests.value.flatMap((address) =>
    (address.guests ?? []).map((guest) => ({
      ...guest,
      address_id: address.id,
      flat_address: formatAddress(address),
      full_name: [guest.name, guest.surname].filter(Boolean).join(' '),
    })),
  ),
)

function boolLabel(value) {
  return value ? 'Tak' : 'Nie'
}

function groomGuestLabel(value) {
  if (value === true) {
    return 'Pan Młody'
  }

  if (value === false) {
    return 'Panna Młoda'
  }

  return 'Wspólny'
}

async function loadGuests() {
  try {
    guests.value = await guestsTable()
  } catch {
    guests.value = []
  }

  expandedRowGroups.value = guests.value
    .filter((address) => address.guests?.length)
    .map((address) => address.id)
}

onMounted(loadGuests)
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <ToggleSwitch v-model="groupByAddress" inputId="group-by-address"/>
        <label for="group-by-address">Grupuj po adresach</label>
      </div>
      <Button label="Dodaj gościa" icon="" class="black-button" @click="isModalOpen = true"/>
    </div>

    <AddGuestForm v-model:visible="isModalOpen" @created="loadGuests"/>

    <div class="mt-6 table">
      <DataTable
          :value="guestRows"
          class="guest-table"
          dataKey="id"
          :rowGroupMode="groupByAddress ? 'subheader' : null"
          :groupRowsBy="groupByAddress ? 'address_id' : null"
          v-model:expandedRowGroups="expandedRowGroups"
          :expandableRowGroups="groupByAddress"
          sortMode="single"
          :sortField="groupByAddress ? 'flat_address' : 'full_name'"
          sortOrder="1"
     >
        <Column field="full_name" header="Gość" />
        <Column
            v-if="!groupByAddress"
            field="flat_address"
            header="Adres"
            style="text-align: center"
        />
        <Column header="Znajomość">
          <template #body="{data}">
            <Tag
                :value="optionLabel(acquaintanceTypes, data.acquaintance_type)"
                :severity="acquaintanceTypeSeverities[data.acquaintance_type]"
            />
          </template>
        </Column>
        <Column header="Dieta">
          <template #body="{data}">
            <Tag
                :value="optionLabel(diets, data.diet)"
                :severity="dietSeverities[data.diet]"
            />
          </template>
        </Column>
        <Column header="Dziecko">
          <template #body="{data}">
            <Tag
                :value="boolLabel(data.child)"
                :severity="data.child ? 'success' : 'secondary'"
            />
          </template>
        </Column>
        <Column header="Strona">
          <template #body="{data}">
            <Tag :value="groomGuestLabel(data.groom_guest)"/>
          </template>
        </Column>
        <Column header="Zaproszenie przygotowane">
          <template #body="{data}">
            <Tag
                :value="boolLabel(data.invitation_prepared)"
                :severity="data.invitation_prepared ? 'success' : 'secondary'"
            />
          </template>
        </Column>
        <Column header="Zaproszenie wysłane">
          <template #body="{data}">
            <Tag
                :value="boolLabel(data.invitation_delivered)"
                :severity="data.invitation_delivered ? 'success' : 'secondary'"
            />
          </template>
        </Column>
        <Column header="Potwierdzenie">
          <template #body="{data}">
            <Tag
                :value="boolLabel(data.confirmed_attendance)"
                :severity="data.confirmed_attendance ? 'success' : 'secondary'"
            />
          </template>
        </Column>
        <template #empty>Brak gości</template>
        <template #groupheader="{data}">
          <span>{{ data.flat_address }}</span>
        </template>
      </DataTable>
    </div>
  </div>
</template>

<style scoped>
  .table {
    color: white;
    width: 100%;
    background: black;
    border-radius: 15px;
  }

  .guest-table {
    border-radius: 15px;
    overflow: hidden;
  }
</style>
