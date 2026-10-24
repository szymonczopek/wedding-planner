import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/auth/LoginView.vue'
import RegisterView from '../views/auth/RegisterView.vue'
import { getAuthToken } from '../api/client.js'
import AppLayout from '../layouts/AppLayout.vue'
import DesktopView from '../views/main/DesktopView.vue'
import GuestsView from '../views/main/GuestsView.vue'
import TablesView from "../views/main/TablesView.vue";
import FinancesView from "../views/main/FinancesView.vue";
import CalendarView from "../views/main/CalendarView.vue";
import WeddingDayView from "../views/main/WeddingDayView.vue";
import CreateOrJoinWeddingView from "../views/auth/CreateOrJoinWeddingView.vue";
import ChooseWeddingView from "../views/auth/ChooseWeddingView.vue";

const routes = [
  {
    path: '/',
    component: AppLayout,
    meta: { requiresAuth: true, requiresWedding: true },
    children: [
      { path: '', component: DesktopView },
      { path: 'guests', component: GuestsView },
      { path: 'tables', component: TablesView },
      { path: 'finances', component: FinancesView },
      { path: 'calendar', component: CalendarView },
      { path: 'wedding-day', component: WeddingDayView }
    ],
  },
  { path: '/login', component: LoginView, meta: { guestOnly: true } },
  { path: '/register', component: RegisterView, meta: { guestOnly: true } },
  { path: '/create-wedding', component: CreateOrJoinWeddingView, meta: { requiresAuth: true } },
  { path: '/choose-wedding', component: ChooseWeddingView, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to) => {
  const loggedIn = !!getAuthToken()
  if (to.meta.requiresAuth && !loggedIn) {
    return { path: '/login', query: { redirect: to.fullPath } }
  }
  if (to.meta.guestOnly && loggedIn) {
    return { path: '/' }
  }
  if (!loggedIn) {
    return
  }

  const { useAuth } = await import('../composables/useAuth.js')
  const { user, fetchUser } = useAuth()
  if (!user.value) {
    await fetchUser()
  }

  if (!user.value) {
    return { path: '/login', query: { redirect: to.fullPath } }
  }

  const selectedWeddings = user.value.weddings ?? []
  const totalWeddings = user.value.weddings_count ?? 0
  const hasSelectedWedding = selectedWeddings.length > 0

  if (to.path === '/create-wedding') {
    return
  }

  if (to.path === '/choose-wedding') {
    if (totalWeddings === 0) {
      return { path: '/create-wedding' }
    }

    if (totalWeddings === 1) {
      return { path: '/' }
    }
    return
  }

  if (!hasSelectedWedding) {
    return { path: totalWeddings === 0 ? '/create-wedding' : '/choose-wedding' }
  }

  return
})

export default router
