<template>
  <div class="min-h-[calc(100vh-56px)] flex items-center justify-center px-4 py-12 bg-dark-800">
    <!-- Background pattern -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-primary-600/5 rounded-full blur-3xl"></div>
      <div class="absolute bottom-1/3 left-1/4 w-96 h-96 bg-primary-800/5 rounded-full blur-3xl"></div>
    </div>

    <div class="auth-card relative z-10">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center mx-auto mb-4">
          <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-1">{{ $t('auth.registerTitle') }}</h1>
        <p class="text-sm text-gray-500">{{ $t('auth.registerSubtitle') }}</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleRegister" class="space-y-4">
        <!-- Error message -->
        <div v-if="authStore.error" class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3">
          <p class="text-xs text-red-400">{{ authStore.error }}</p>
        </div>

        <!-- Email -->
        <div>
          <label class="block text-xs font-medium text-gray-400 mb-1.5">{{ $t('auth.email') }}</label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
            </svg>
            <input
              v-model="form.email"
              type="email"
              class="input-field pl-10"
              placeholder="you@example.com"
              required
            />
          </div>
        </div>

        <!-- Username -->
        <div>
          <label class="block text-xs font-medium text-gray-400 mb-1.5">{{ $t('auth.username') }}</label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <input
              v-model="form.username"
              type="text"
              class="input-field pl-10"
              placeholder="Choose a username"
              required
              minlength="3"
            />
          </div>
        </div>

        <!-- Password -->
        <div>
          <label class="block text-xs font-medium text-gray-400 mb-1.5">{{ $t('auth.password') }}</label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="input-field pl-10 pr-10"
              placeholder="Min. 8 characters"
              required
              minlength="8"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-400"
            >
              <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
              </svg>
            </button>
          </div>
          <!-- Password strength -->
          <div class="flex gap-1 mt-2">
            <div
              v-for="i in 4"
              :key="i"
              :class="[
                'h-1 flex-1 rounded-full transition-colors',
                i <= passwordStrength ? strengthColors[passwordStrength - 1] : 'bg-dark-400'
              ]"
            ></div>
          </div>
          <p v-if="passwordStrength > 0" :class="['text-[10px] mt-1', strengthTextColors[passwordStrength - 1]]">
            {{ strengthLabels[passwordStrength - 1] }}
          </p>
        </div>

        <!-- Confirm Password -->
        <div>
          <label class="block text-xs font-medium text-gray-400 mb-1.5">{{ $t('auth.confirmPassword') }}</label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <input
              v-model="form.confirmPassword"
              type="password"
              class="input-field pl-10"
              placeholder="Repeat your password"
              required
            />
          </div>
          <p v-if="form.confirmPassword && form.password !== form.confirmPassword" class="text-[10px] text-red-400 mt-1">
            Passwords do not match
          </p>
        </div>

        <!-- Terms -->
        <label class="flex items-start gap-2 cursor-pointer">
          <input
            v-model="form.agreeTerms"
            type="checkbox"
            class="mt-0.5 w-4 h-4 bg-dark-600 border-dark-300 rounded text-primary-500 focus:ring-primary-500/30"
            required
          />
          <span class="text-xs text-gray-400 leading-relaxed">{{ $t('auth.termsAgree') }}</span>
        </label>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="authStore.loading || !isFormValid"
          class="btn-primary"
        >
          <span v-if="authStore.loading" class="flex items-center justify-center gap-2">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Loading...
          </span>
          <span v-else>{{ $t('auth.signUp') }}</span>
        </button>
      </form>

      <!-- Login link -->
      <p class="text-center mt-6 text-xs text-gray-500">
        {{ $t('auth.hasAccount') }}
        <router-link to="/login" class="text-primary-400 hover:text-primary-300 font-medium transition-colors">
          {{ $t('auth.signIn') }}
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../store/auth';

const router = useRouter();
const authStore = useAuthStore();

const showPassword = ref(false);

const form = reactive({
  email: '',
  username: '',
  password: '',
  confirmPassword: '',
  agreeTerms: false,
});

const strengthColors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
const strengthTextColors = ['text-red-400', 'text-orange-400', 'text-yellow-400', 'text-green-400'];
const strengthLabels = ['Weak', 'Fair', 'Good', 'Strong'];

const passwordStrength = computed(() => {
  const pwd = form.password;
  if (!pwd) return 0;
  let score = 0;
  if (pwd.length >= 8) score++;
  if (/[A-Z]/.test(pwd)) score++;
  if (/[0-9]/.test(pwd)) score++;
  if (/[^A-Za-z0-9]/.test(pwd)) score++;
  return score;
});

const isFormValid = computed(() => {
  return form.email &&
    form.username &&
    form.password.length >= 8 &&
    form.password === form.confirmPassword &&
    form.agreeTerms;
});

async function handleRegister() {
  if (!isFormValid.value) return;
  try {
    await authStore.register({
      email: form.email,
      username: form.username,
      password: form.password,
    });
    router.push('/');
  } catch {
    // Error handled by store
  }
}
</script>
