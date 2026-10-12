import {ref, computed} from 'vue'
import router from '../router/index.js'
import api, {setAuthToken, clearAuthToken, getAuthToken} from "../api/client.js";

const user = ref(null)
const isLoading = ref(false)

export function useAuth() {
    const isAuthenticated = computed(() => user.value !== null)

    async function login(email, password) {
        isLoading.value = true
        try {
            const {data} = await api.post('/login', {email, password})
            setAuthToken(data.token)

            user.value = data.user
            await resolveWedding(data.user?.weddings)
        } finally {
            isLoading.value = false
        }
    }

    async function resolveWedding(weddings) {
        const weddingsCount = weddings?.length || 0

        if (weddingsCount === 0) {
            await router.push('/create-wedding')
        } else if (weddingsCount > 1) {
            await router.push('/choose-wedding')
        } else {
            await router.push('/')
        }
    }

    async function register(name, email, password, password_confirmation) {
        isLoading.value = true
        try {
            const {data} = await api.post('/register', {name, email, password, password_confirmation})
            setAuthToken(data.token)
            user.value = data.user
            await resolveWedding(data.user?.weddings)
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
            const {data} = await api.get('/me')
            user.value = data
        } catch {
            clearAuthToken()
            user.value = null
        }
    }


    return {user, isAuthenticated, isLoading, login, register, logout, fetchUser}
}
