import api from '../api/client.js'

export function useGuest() {
  async function searchAddresses(query) {
    const { data } = await api.get('/addresses/search', {
      params: { q: query },
    })

    return data ?? []
  }

  async function searchPolishAddresses(query) {
    const { data } = await api.get('/addresses/autocomplete', {
      params: { q: query },
    })

    return data ?? []
  }

  async function fetchGuestsList() {
    const { data } = await api.get('/guests/list')

    return data ?? []
  }

  async function addressesTable({ first = 0, rowsPerPage = 10 } = {}) {
    const { data } = await api.get('/guests/address-table', {
      params: { first, rowsPerPage },
    })

    return { data: data?.data ?? [], total: data?.total ?? 0 }
  }

  async function guestsTable({ first = 0, rowsPerPage = 10 } = {}) {
    const { data } = await api.get('/guests/guest-table', {
      params: { first, rowsPerPage },
    })

    return { data: data?.data ?? [], total: data?.total ?? 0 }
  }

  return { searchAddresses, searchPolishAddresses, fetchGuestsList, addressesTable, guestsTable }
}
