import api from "../api/client.js";
import {ref} from "vue";
import router from "../router/index.js";


const isCreating = ref(false)
const isJoining = ref(false)
export function useWedding() {
async function createWedding(name, date, selectedRole) {
    isCreating.value = true
    try {
        await api.post('/weddings/create', {name, date, role: selectedRole})
    } finally {
        isCreating.value = false
    }
    router.push('/')
}

async function joinToWedding(inviteCode, selectedRole) {
    isJoining.value = true
    try {
        await api.post('/join-wedding', {inviteCode, selectedRole})
    } finally {
        isJoining.value = false
        router.push('/')
    }
}
    return {createWedding, joinToWedding, isCreating, isJoining}
}