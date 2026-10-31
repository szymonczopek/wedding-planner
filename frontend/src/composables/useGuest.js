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

  return { searchAddresses, searchPolishAddresses }
}
