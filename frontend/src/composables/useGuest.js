import api from '../api/client.js'

export function useGuest() {
  async function searchAddresses(weddingId, query) {
    const { data } = await api.get('/addresses/search', {
      params: { wedding_id: weddingId, q: query },
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
