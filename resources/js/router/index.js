import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/sports/Home.vue'),
  },
  {
    path: '/live',
    name: 'live',
    component: () => import('../views/sports/Home.vue'),
    meta: { liveOnly: true },
  },
  {
    path: '/sport',
    name: 'sport',
    component: () => import('../views/sports/Home.vue'),
  },
  {
    path: '/casino',
    name: 'casino',
    component: () => import('../views/casino/Casino.vue'),
  },
  {
    path: '/slots',
    name: 'slots',
    component: () => import('../views/casino/Casino.vue'),
    meta: { category: 'slots' },
  },
  {
    path: '/live-casino',
    name: 'live-casino',
    component: () => import('../views/casino/Casino.vue'),
    meta: { category: 'live-casino' },
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/auth/Login.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/auth/Register.vue'),
    meta: { guest: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

export default router;
