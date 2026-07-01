import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

const API_BASE = '/api';

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
      currency: 'EUR',
      minimumFractionDigits: 2,
    }).format(balance.value);
  });

  function setAuthHeader() {
    if (token.value) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
    } else {
      delete axios.defaults.headers.common['Authorization'];
    }
  }

  async function login(credentials) {
    loading.value = true;
    error.value = null;
    try {
      const { data } = await axios.post(`${API_BASE}/auth/login`, {
        login: credentials.email || credentials.username,
        password: credentials.password,
      });

      token.value = data.token;
      user.value = data.user;
      localStorage.setItem('auth_token', token.value);
      setAuthHeader();
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
      const { data: res } = await axios.post(`${API_BASE}/auth/register`, {
        username: data.username,
        email: data.email,
        password: data.password,
        password_confirmation: data.password_confirmation,
      });

      token.value = res.token;
      user.value = res.user;
      localStorage.setItem('auth_token', token.value);
      setAuthHeader();
    } catch (e) {
      error.value = e.response?.data?.message || e.response?.data?.errors ? Object.values(e.response.data.errors).flat().join(', ') : 'Registration failed';
      throw e;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    try {
      if (token.value) {
        await axios.post(`${API_BASE}/auth/logout`);
      }
    } catch {
      // ignore logout errors
    } finally {
      user.value = null;
      token.value = null;
      localStorage.removeItem('auth_token');
      setAuthHeader();
    }
  }

  async function fetchUser() {
    if (!token.value) return;
    setAuthHeader();
    try {
      const { data } = await axios.get(`${API_BASE}/auth/me`);
      user.value = data.data || data.user || data;
    } catch {
      logout();
    }
  }

  async function refreshBalance() {
    if (!token.value) return;
    try {
      const { data } = await axios.get(`${API_BASE}/user/balance`);
      if (user.value) {
        user.value.balance = data.data?.balance ?? data.balance;
      }
    } catch {
      // silent fail
    }
  }

  // Initialize auth header on store creation
  setAuthHeader();

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
    refreshBalance,
  };
});
