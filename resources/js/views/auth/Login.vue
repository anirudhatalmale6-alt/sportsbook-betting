<template>
  <div class="min-h-[calc(100vh-48px)] flex items-center justify-center px-4 py-12 bg-dark-900">
    <div class="w-full max-w-sm bg-gradient-to-b from-dark-500/80 to-dark-700/90 rounded-xl p-6 border border-dark-400/20">
      <h1 class="text-xl font-bold text-white mb-5">{{ $t('auth.loginTitle') }}</h1>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div v-if="authStore.error" class="bg-red-500/10 border border-red-500/30 rounded px-3 py-2">
          <p class="text-xs text-red-400">{{ authStore.error }}</p>
        </div>

        <div>
          <label class="block text-sm text-gray-400 mb-1">{{ $t('auth.username') }}</label>
          <input
            v-model="form.username"
            type="text"
            class="w-full bg-white text-dark-800 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
            required
          />
        </div>

        <div>
          <label class="block text-sm text-gray-400 mb-1">{{ $t('auth.password') }}</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full bg-white text-dark-800 rounded px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
            required
          />
        </div>

        <button
          type="submit"
          :disabled="authStore.loading"
          class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded transition-colors mt-2"
        >
          <span v-if="authStore.loading" class="flex items-center justify-center gap-2">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Loading...
          </span>
          <span v-else>{{ $t('auth.loginTitle') }}</span>
        </button>
      </form>

      <p class="text-center mt-5 text-xs text-gray-500">
        {{ $t('auth.noAccount') }}
        <router-link to="/register" class="text-primary-400 hover:text-primary-300 font-medium">
          {{ $t('auth.signUp') }}
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../store/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  username: '',
  password: '',
});

async function handleLogin() {
  try {
    await authStore.login({
      username: form.username,
      password: form.password,
    });
    router.push('/');
  } catch {
    // Error handled by store
  }
}
</script>
