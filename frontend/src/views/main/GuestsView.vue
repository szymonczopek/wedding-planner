<script setup>
import {computed, onMounted, ref} from 'vue'
import Button from 'primevue/button'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import Paginator from 'primevue/paginator'
import Tag from 'primevue/tag'
import ToggleSwitch from 'primevue/toggleswitch'
import Tooltip from 'primevue/tooltip'
import AddGuestForm from '../../components/forms/AddGuestForm.vue'
import {
  acquaintanceTypes,
  acquaintanceTypeSeverities,
  isGroomGuestTypeSeverities,
  diets,
  dietSeverities,
  optionLabel,
} from '../../constants/guests.js'
import {useGuest} from '../../composables/useGuest.js'

const {addressesTable, guestsPage} = useGuest()

const vTooltip = Tooltip

const isModalOpen = ref(false)
const tableData = ref([])
const expandedRowGroups = ref([])
const groupByAddress = ref(true)
const selectedGuest = ref([]);
const rowsPerPage = ref(10)
const first = ref(0)
const totalRecords = ref(0)

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

function guestFullName(guest) {
  return [guest.name, guest.surname].filter(Boolean).join(' ')
}

function flattenAddresses(addresses) {
  return addresses.flatMap((address) => {
    const flatAddress = formatAddress(address)

    return (address.guests ?? []).flatMap((guest) => {
      const fullName = guestFullName(guest)
      const mainSortName = [fullName, guest.id].join('\u0000')
      const mainRow = {
        ...guest,
        address_id: address.id,
        flat_address: flatAddress,
        full_name: fullName,
        is_companion: false,
        sort_name: mainSortName,
        group_sort_key: [flatAddress, address.id, mainSortName].join('\u0000'),
      }

      const companion = Array.isArray(guest.companion)
        ? guest.companion[0] ?? null
        : guest.companion

      if (!companion) {
        return [mainRow]
      }

      const companionSortName = `${mainSortName}\uffff`

      return [
        mainRow,
        {
          ...companion,
          address_id: address.id,
          flat_address: flatAddress,
          full_name: guestFullName(companion),
          is_companion: true,
          main_guest_name: fullName,
          sort_name: companionSortName,
          group_sort_key: [flatAddress, address.id, companionSortName].join('\u0000'),
        },
      ]
    })
  })
}

function flattenGuests(guestsList) {
  return guestsList.flatMap((guest) => {
    const flatAddress = guest.address ? formatAddress(guest.address) : ''
    const fullName = guestFullName(guest)
    const mainRow = {
      ...guest,
      address_id: guest.address_id ?? guest.address?.id ?? null,
      flat_address: flatAddress,
      full_name: fullName,
      is_companion: false,
    }

    const companion = Array.isArray(guest.companion)
      ? guest.companion[0] ?? null
      : guest.companion

    if (!companion) {
      return [mainRow]
    }

    return [
      mainRow,
      {
        ...companion,
        address_id: mainRow.address_id,
        flat_address: flatAddress,
        full_name: guestFullName(companion),
        is_companion: true,
        main_guest_name: fullName,
      },
    ]
  })
}

const guestRows = computed(() =>
  groupByAddress.value
    ? flattenAddresses(tableData.value)
    : flattenGuests(tableData.value),
)

function boolLabel(value) {
  return value ? 'Tak' : 'Nie'
}

function isGroomGuestLabel(value) {
  if (value === true) {
    return 'Pan Młody'
  }

  if (value === false) {
    return 'Panna Młoda'
  }

  return 'Wspólny'
}

let requestToken = 0

async function loadGuests() {
  const token = ++requestToken
  const params = {first: first.value, rowsPerPage: rowsPerPage.value}

  try {
    const response = groupByAddress.value
      ? await addressesTable(params)
      : await guestsPage(params)

    if (token !== requestToken) {
      return
    }

    tableData.value = response.data
    totalRecords.value = response.total
  } catch {
    if (token !== requestToken) {
      return
    }

    tableData.value = []
    totalRecords.value = 0
  }

  expandedRowGroups.value = groupByAddress.value
    ? tableData.value
      .filter((address) => address.guests?.length)
      .map((address) => address.id)
    : []
}

function onPageChange(event) {
  first.value = event.first
  rowsPerPage.value = event.rows
  loadGuests()
}

function onGroupChange(value) {
  groupByAddress.value = value
  first.value = 0
  loadGuests()
}

function onCreated() {
  first.value = 0
  loadGuests()
}

onMounted(loadGuests)
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <ToggleSwitch
            v-model="groupByAddress"
            inputId="group-by-address"
            @update:model-value="onGroupChange"
        />
        <label for="group-by-address">Grupuj po adresach</label>
        <Button label="Usuń zaznaczenie" v-if="selectedGuest.length > 0" @click="() => {selectedGuest = []}"></Button>
      </div>
      <Button label="Dodaj gościa" @click="isModalOpen = true"/>
    </div>

    <AddGuestForm v-model:visible="isModalOpen" @created="onCreated"/>

    <div class="mt-6 table">
      <DataTable
          :value="guestRows"
          class="guest-table"
          dataKey="id"
          :rowGroupMode="groupByAddress ? 'subheader' : null"
          :groupRowsBy="groupByAddress ? 'address_id' : null"
          v-model:expandedRowGroups="expandedRowGroups"
          :expandableRowGroups="groupByAddress"
          row-hover
          :rowClass="(data) => (data.is_companion ? 'guest-row companion-row' : 'guest-row')"
          v-model:selection="selectedGuest"
          selectionMode="multiple"
     >
        <Column header="Gość">
          <template #body="{data}">
            <div class="guest-name">
              <span>{{ data.full_name }}</span>
              <span
                  v-if="data.is_companion"
                  v-tooltip.top="`Główny gość: ${data.main_guest_name}`"
                  class="companion-label"
              >Osoba Towarzysząca</span>
            </div>
          </template>
        </Column>
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
            <Tag
                :value="isGroomGuestLabel(data.groom_guest)"
                :severity="isGroomGuestTypeSeverities[data.groom_guest]"
            />
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

      <Paginator
          class="mt-4"
          v-model:first="first"
          v-model:rows="rowsPerPage"
          :totalRecords="totalRecords"
          :rowsPerPageOptions="[5, 10, 20, 50]"
          template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown CurrentPageReport"
          currentPageReportTemplate="{first}–{last} z {totalRecords}"
          @page="onPageChange"
      />
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

  .guest-table :deep(.companion-row) > td {
    background: rgba(255, 255, 255, 0.04);
  }

  .guest-table :deep(.guest-row) > td:first-child {
    padding-left: 5rem;
  }

  .guest-name {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .companion-label {
    margin-top: 0.15rem;
    color: #a3a3a3;
    cursor: help;
  }
</style>
