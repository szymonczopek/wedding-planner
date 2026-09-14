import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import { getAuthToken } from '@/api/client.js'
import AppLayout from '@/layouts/AppLayout.vue'
import MainDesktopView from '@/views/MainDesktopView.vue'
import GuestsView from '@/views/GuestsView.vue'
import TablesView from "../views/TablesView.vue";
import FinancesView from "../views/FinancesView.vue";
import CalendarView from "../views/CalendarView.vue";
import WeddingDayView from "../views/WeddingDayView.vue";

const routes = [
  {
    path: '/',
    component: AppLayout,
    meta: { requiresAuth: true },
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
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const loggedIn = !!getAuthToken()
  if (to.meta.requiresAuth && !loggedIn) {
    return { path: '/login', query: { redirect: to.fullPath } }
  }
  if (to.meta.guestOnly && loggedIn) {
    return { path: '/' }
  }
})

export default router
