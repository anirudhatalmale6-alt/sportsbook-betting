import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('auth_token') || null);
  const loading = ref(false);
  const error = ref(null);

  const isAuthenticated = computed(() => !!token.value);
  const balance = computed(() => user.value?.balance ?? 0);
  const formattedBalance = computed(() => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: user.value?.currency ?? 'EUR',
      minimumFractionDigits: 2,
    }).format(balance.value);
  });

  async function login(credentials) {
    loading.value = true;
    error.value = null;
    try {
      // Simulated API call
      await new Promise(resolve => setTimeout(resolve, 800));

      // Demo user
      token.value = 'demo-token-' + Date.now();
      user.value = {
        id: 1,
        username: credentials.username || 'Player1',
        email: credentials.email || 'player@example.com',
        balance: 1250.00,
        currency: 'EUR',
        avatar: null,
      };
      localStorage.setItem('auth_token', token.value);
    } catch (e) {
      error.value = e.response?.data?.message || 'Login failed';
      throw e;
    } finally {
      loading.value = false;
    }
  }

  async function register(data) {
    loading.value = true;
    error.value = null;
    try {
      await new Promise(resolve => setTimeout(resolve, 800));

      token.value = 'demo-token-' + Date.now();
      user.value = {
        id: 2,
        username: data.username,
        email: data.email,
        balance: 0.00,
        currency: 'EUR',
        avatar: null,
      };
      localStorage.setItem('auth_token', token.value);
    } catch (e) {
      error.value = e.response?.data?.message || 'Registration failed';
      throw e;
    } finally {
      loading.value = false;
    }
  }

  function logout() {
    user.value = null;
    token.value = null;
    localStorage.removeItem('auth_token');
  }

  async function fetchUser() {
    if (!token.value) return;
    try {
      // Demo: restore session
      user.value = {
        id: 1,
        username: 'Player1',
        email: 'player@example.com',
        balance: 1250.00,
        currency: 'EUR',
        avatar: null,
      };
    } catch {
      logout();
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    balance,
    formattedBalance,
    login,
    register,
    logout,
    fetchUser,
  };
});
