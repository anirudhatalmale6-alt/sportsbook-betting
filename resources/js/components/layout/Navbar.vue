<template>
  <nav class="bg-[#0d0d0d] border-b border-dark-400/30 sticky top-0 z-40">
    <!-- Main bar -->
    <div class="max-w-[1440px] mx-auto px-3 sm:px-4">
      <div class="flex items-center justify-between h-12">
        <!-- Left: Hamburger (mobile only) -->
        <div class="flex items-center lg:hidden">
          <button
            @click="$emit('toggleSidebar')"
            class="flex flex-col gap-1 p-2"
          >
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
          </button>
        </div>

        <!-- Center: Nav links (desktop) -->
        <div class="hidden lg:flex items-center gap-0 flex-1 justify-center">
          <router-link
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="jilbet-nav-link"
          >
            <span v-if="link.hasLiveDot" class="live-dot"></span>
            {{ link.label }}
          </router-link>
        </div>

        <!-- Right: Controls -->
        <div class="flex items-center gap-2">
          <!-- Language selector -->
          <div class="relative" ref="langDropdownRef">
            <button
              @click="showLangDropdown = !showLangDropdown"
              class="flex items-center gap-1.5 px-2 py-1.5 text-xs text-gray-300 hover:text-white transition-colors"
            >
              <span>{{ currentLocaleFlag }}</span>
              <span class="hidden sm:inline text-gray-400">{{ currentLocaleName }}</span>
              <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div
              v-if="showLangDropdown"
              class="absolute right-0 top-full mt-1 bg-dark-500 border border-dark-300/40 rounded-lg shadow-xl py-1 max-h-80 overflow-y-auto min-w-[160px] z-50"
            >
              <button
                v-for="loc in locales"
                :key="loc.code"
                @click="changeLocale(loc.code)"
                :class="[
                  'w-full text-left px-3 py-2 text-xs hover:bg-dark-400 transition-colors flex items-center gap-2',
                  locale === loc.code ? 'text-primary-400' : 'text-gray-300'
                ]"
              >
                <span>{{ loc.flag }}</span>
                <span>{{ loc.name }}</span>
              </button>
            </div>
          </div>

          <!-- Odds format selector (desktop) -->
          <div class="hidden xl:block relative" ref="oddsDropdownRef">
            <button
              @click="showOddsDropdown = !showOddsDropdown"
              class="flex items-center gap-1.5 px-2 py-1.5 text-xs text-gray-400 hover:text-white transition-colors"
            >
              <span>{{ oddsFormats.find(f => f.value === oddsFormat)?.shortLabel }}</span>
              <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div
              v-if="showOddsDropdown"
              class="absolute right-0 top-full mt-1 bg-dark-500 border border-dark-300/40 rounded-lg shadow-xl py-1 min-w-[140px] z-50"
            >
              <button
                v-for="fmt in oddsFormats"
                :key="fmt.value"
                @click="oddsFormat = fmt.value; showOddsDropdown = false"
                :class="[
                  'w-full text-left px-3 py-2 text-xs hover:bg-dark-400 transition-colors',
                  oddsFormat === fmt.value ? 'text-primary-400' : 'text-gray-300'
                ]"
              >
                {{ fmt.label }}
              </button>
            </div>
          </div>

          <!-- Search icon -->
          <button class="p-2 text-gray-400 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </button>

          <!-- User info (logged in) -->
          <div v-if="authStore.isAuthenticated" class="hidden sm:flex items-center gap-2">
            <span class="text-xs text-gray-300">
              {{ authStore.user?.username || 'User' }}
              <span class="text-gray-500">({{ authStore.user?.id || '' }})</span>
              <span class="text-primary-400 font-semibold ml-1">{{ authStore.formattedBalance }}</span>
            </span>
            <div class="w-7 h-7 rounded-full bg-dark-400 border border-dark-200/40 flex items-center justify-center">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
          </div>

          <!-- Login/Register (not logged in) -->
          <div v-else class="flex items-center gap-2">
            <router-link to="/login" class="text-xs text-gray-300 hover:text-white transition-colors px-3 py-1.5">
              {{ $t('nav.login') }}
            </router-link>
            <router-link to="/register" class="text-xs bg-primary-600 hover:bg-primary-700 text-white px-4 py-1.5 rounded transition-colors font-medium">
              {{ $t('nav.register') }}
            </router-link>
          </div>

          <!-- Betslip toggle -->
          <button
            @click="betslipStore.toggleOpen()"
            class="relative p-2 text-gray-400 hover:text-white transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span
              v-if="betslipStore.selectionCount > 0"
              class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-primary-500 rounded-full text-[10px] font-bold text-white flex items-center justify-center"
            >
              {{ betslipStore.selectionCount }}
            </span>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile nav row -->
    <div class="lg:hidden border-t border-dark-400/20">
      <div class="flex overflow-x-auto hide-scrollbar px-2">
        <router-link
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          class="jilbet-nav-link whitespace-nowrap text-xs py-2.5"
        >
          <span v-if="link.hasLiveDot" class="live-dot"></span>
          {{ link.label }}
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../../store/auth';
import { useBetslipStore } from '../../store/betslip';
import { availableLocales } from '../../i18n';

defineEmits(['toggleSidebar']);

const { t, locale } = useI18n();
const route = useRoute();
const authStore = useAuthStore();
const betslipStore = useBetslipStore();

const showLangDropdown = ref(false);
const showOddsDropdown = ref(false);
const langDropdownRef = ref(null);
const oddsDropdownRef = ref(null);
const oddsFormat = ref(localStorage.getItem('odds_format') || 'decimal');
const locales = availableLocales;

const oddsFormats = [
  { value: 'decimal', label: 'Decimal (1.50)', shortLabel: 'Decimal' },
  { value: 'fractional', label: 'Fractional (1/2)', shortLabel: 'Fractional' },
  { value: 'american', label: 'American (-200)', shortLabel: 'American' },
];

const currentLocaleFlag = computed(() => {
  return locales.find(l => l.code === locale.value)?.flag || '🌐';
});

const currentLocaleName = computed(() => {
  return locales.find(l => l.code === locale.value)?.name || 'English';
});

const navLinks = computed(() => [
  { to: '/live', label: t('nav.live'), hasLiveDot: true },
  { to: '/sport', label: t('nav.sport'), hasLiveDot: false },
  { to: '/', label: t('nav.wetten'), hasLiveDot: false },
  { to: '/casino', label: t('nav.casino'), hasLiveDot: false },
  { to: '/slots', label: t('nav.slots'), hasLiveDot: false },
  { to: '/live-casino', label: t('nav.liveCasino'), hasLiveDot: false },
]);

function changeLocale(code) {
  locale.value = code;
  localStorage.setItem('locale', code);
  showLangDropdown.value = false;
  document.documentElement.dir = ['ar', 'he', 'ur'].includes(code) ? 'rtl' : 'ltr';
}

function handleClickOutside(e) {
  if (langDropdownRef.value && !langDropdownRef.value.contains(e.target)) {
    showLangDropdown.value = false;
  }
  if (oddsDropdownRef.value && !oddsDropdownRef.value.contains(e.target)) {
    showOddsDropdown.value = false;
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
