import api from "../api/client.js";
import {ref} from "vue";
import router from "../router/index.js";
import {useAuth} from "./useAuth.js";
const { fetchUser } = useAuth()


const isCreating = ref(false)
const isJoining = ref(false)
export function useWedding() {
async function createWedding(name, date, selectedRole) {
    isCreating.value = true
    try {
        await api.post('/weddings/create', {name, date, role: selectedRole})
        await fetchUser()
        await router.push('/')
    } finally {
        isCreating.value = false
    }
}

async function joinToWedding(inviteCode, selectedRole) {
    isJoining.value = true
    try {
        await api.post('/join-wedding', {inviteCode, selectedRole})
        await fetchUser()
        await router.push('/')
    } finally {
        isJoining.value = false
    }
}
    return {createWedding, joinToWedding, isCreating, isJoining}
}