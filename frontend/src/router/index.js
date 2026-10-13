import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import { getAuthToken } from '../api/client.js'
import AppLayout from '../layouts/AppLayout.vue'
import MainDesktopView from '../views/MainDesktopView.vue'
import GuestsView from '../views/GuestsView.vue'
import TablesView from "../views/TablesView.vue";
import FinancesView from "../views/FinancesView.vue";
import CalendarView from "../views/CalendarView.vue";
import WeddingDayView from "../views/WeddingDayView.vue";
import CreateOrJoinWeddingView from "../views/CreateOrJoinWeddingView.vue";
import ChooseWedding from "../views/ChooseWedding.vue";

const routes = [
  {
    path: '/',
    component: AppLayout,
    meta: { requiresAuth: true, requiresWedding: true },
    children: [
      { path: '', component: MainDesktopView },
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
  { path: '/choose-wedding', component: ChooseWedding, meta: { requiresAuth: true } },
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

  const needsWeddingCheck = to.meta.requiresWedding || to.path === '/choose-wedding'
  if (!needsWeddingCheck) {
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

  const weddingsCount = user.value.weddings?.length ?? 0
  if (to.meta.requiresWedding) {
    if (weddingsCount === 0) {
      return { path: '/create-wedding' }
    }
    if (weddingsCount > 1) {
      return { path: '/choose-wedding' }
    }
    return
  }
  if (to.path === '/choose-wedding' && weddingsCount <= 1) {
    return { path: weddingsCount === 0 ? '/create-wedding' : '/' }
  }
})

export default router
