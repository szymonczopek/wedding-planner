import {ref, computed} from 'vue'
import router from '../router/index.js'
import api, {setAuthToken, clearAuthToken, getAuthToken} from "../api/client.js";

const user = ref(null)
const isLoading = ref(false)

export function useAuth() {
    const isAuthenticated = computed(() => user.value !== null)

    async function getUserWithAllWeddings() {
        const {data} = await api.get('/user-with-weddings')
        return data
    }

    async function login(email, password) {
        isLoading.value = true
        try {
            const {data} = await api.post('/login', {email, password})
            setAuthToken(data.token)

            const me = await getUserWithAllWeddings()
            const allWeddings = me.weddings ?? []
            const hasSelectedWedding = allWeddings.some((w) => w.pivot?.selected === true)

            user.value = null

            if (hasSelectedWedding) {
                await router.push('/')
            } else if (allWeddings.length > 0) {
                await router.push('/choose-wedding')
            } else {
                await router.push('/create-wedding')
            }
        } finally {
            isLoading.value = false
        }
    }

    async function register(name, email, password, password_confirmation) {
        isLoading.value = true
        try {
            const {data} = await api.post('/register', {name, email, password, password_confirmation})
            setAuthToken(data.token)
            user.value = null
            await router.push('/')
        } finally {
            isLoading.value = false
        }
    }

    async function logout() {
        isLoading.value = true
        try {
            await api.post('/logout')
        } finally {
            isLoading.value = false
            clearAuthToken()
            user.value = null
            router.push('/login')
        }
    }

    async function fetchUser() {
        if (!getAuthToken()) return
        try {
            const {data} = await api.get('/wedding-user')
            user.value = data
        } catch {
            clearAuthToken()
            user.value = null
        }
    }

    return {user, isAuthenticated, isLoading, login, register, logout, fetchUser, getUserWithAllWeddings}
}
