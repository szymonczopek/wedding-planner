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

  async function guestsTable() {
    const { data } = await api.get('/guests')

    return data ?? []
  }

  return { searchAddresses, searchPolishAddresses, fetchGuestsList, guestsTable }
}
