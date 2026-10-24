import api from '../api/client.js'
import { ref } from 'vue'
import router from '../router/index.js'
import { useAuth } from './useAuth.js'

const { fetchUser } = useAuth()

const isCreating = ref(false)
const isJoining = ref(false)
const isChoosing = ref(false)

async function runAction(flag, request) {
  flag.value = true
  try {
    await request()
    await fetchUser()
    await router.push('/')
  } finally {
    flag.value = false
  }
}

export function useWedding() {
  async function createWedding(name, date, selectedRole) {
    return runAction(isCreating, () => api.post('/weddings/create', { name, date, role: selectedRole }))
  }

  async function joinToWedding(inviteCode, selectedRole) {
    return runAction(isJoining, () => api.post('/weddings/join', { invite_code: inviteCode, role: selectedRole }))
  }

  async function chooseWedding(wedding_id) {
    return runAction(isChoosing, () => api.post('/weddings/choose', { wedding_id }))
  }

  return { createWedding, joinToWedding, isCreating, isJoining, isChoosing, chooseWedding }
}
