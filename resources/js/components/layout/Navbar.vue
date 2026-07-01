<template>
  <nav class="bg-dark-900 border-b border-dark-400/30 sticky top-0 z-40">
    <!-- Top bar -->
    <div class="max-w-[1440px] mx-auto px-3 sm:px-4">
      <div class="flex items-center justify-between h-14">
        <!-- Left: Hamburger + Logo -->
        <div class="flex items-center gap-3">
          <button
            @click="$emit('toggleSidebar')"
            class="lg:hidden flex flex-col gap-1 p-2"
          >
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
          </button>

          <router-link to="/" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
              </svg>
            </div>
            <span class="text-xl font-bold text-white hidden sm:block">
              <span class="text-primary-400">Bet</span>Zone
            </span>
          </router-link>
        </div>

        <!-- Center: Nav links (desktop) -->
        <div class="hidden lg:flex items-center gap-1">
          <router-link
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            :class="['nav-link', { active: isActiveLink(link.to) }]"
          >
            <span v-if="link.badge" class="badge-live mr-1.5">{{ link.badge }}</span>
            {{ link.label }}
          </router-link>
        </div>

        <!-- Right: Controls -->
        <div class="flex items-center gap-2">
          <!-- Odds format selector (desktop) -->
          <div class="hidden xl:block relative" ref="oddsDropdownRef">
            <button
              @click="showOddsDropdown = !showOddsDropdown"
              class="btn-secondary text-xs flex items-center gap-1.5"
            >
              <span>{{ oddsFormats.find(f => f.value === oddsFormat)?.label }}</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

          <!-- Language selector -->
          <div class="relative" ref="langDropdownRef">
            <button
              @click="showLangDropdown = !showLangDropdown"
              class="btn-secondary text-xs flex items-center gap-1.5"
            >
              <span>{{ currentLocaleFlag }}</span>
              <span class="hidden sm:inline">{{ currentLocaleCode.toUpperCase() }}</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

          <!-- Balance (logged in) -->
          <div v-if="authStore.isAuthenticated" class="hidden sm:flex items-center gap-2">
            <div class="bg-dark-600 rounded-lg px-3 py-1.5 flex items-center gap-2">
              <svg class="w-4 h-4 text-accent-gold" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-2h-1c-1.1 0-2-.9-2-2V9c0-1.1.9-2 2-2h2c1.1 0 2 .9 2 2v1h-2V9h-2v4h1c1.1 0 2 .9 2 2v2c0 1.1-.9 2-2 2h-2c-1.1 0-2-.9-2-2v-1h2v1z"/>
              </svg>
              <span class="text-sm font-semibold text-accent-gold">{{ authStore.formattedBalance }}</span>
            </div>
            <button class="btn-primary !w-auto !py-1.5 text-xs">
              {{ $t('nav.deposit') }}
            </button>
          </div>

          <!-- Login/Register (not logged in) -->
          <div v-else class="flex items-center gap-2">
            <router-link to="/login" class="btn-secondary text-xs">
              {{ $t('nav.login') }}
            </router-link>
            <router-link to="/register" class="btn-primary !w-auto text-xs !py-2">
              {{ $t('nav.register') }}
            </router-link>
          </div>

          <!-- Betslip toggle -->
          <button
            @click="betslipStore.toggleOpen()"
            class="relative p-2 rounded-lg bg-dark-400 hover:bg-dark-300 transition-colors"
          >
            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span
              v-if="betslipStore.selectionCount > 0"
              class="absolute -top-1 -right-1 w-4 h-4 bg-primary-500 rounded-full text-[10px] font-bold text-white flex items-center justify-center"
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
          :class="['nav-link whitespace-nowrap text-xs py-2.5', { active: isActiveLink(link.to) }]"
        >
          <span v-if="link.badge" class="badge-live mr-1 text-[8px]">{{ link.badge }}</span>
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
  { value: 'decimal', label: 'Decimal (1.50)' },
  { value: 'fractional', label: 'Fractional (1/2)' },
  { value: 'american', label: 'American (-200)' },
];

const currentLocaleFlag = computed(() => {
  return locales.find(l => l.code === locale.value)?.flag || '🌐';
});

const currentLocaleCode = computed(() => locale.value);

const navLinks = computed(() => [
  { to: '/live', label: t('nav.live'), badge: 'LIVE' },
  { to: '/sport', label: t('nav.sport') },
  { to: '/', label: t('nav.wetten') },
  { to: '/casino', label: t('nav.casino') },
  { to: '/slots', label: t('nav.slots') },
  { to: '/live-casino', label: t('nav.liveCasino') },
]);

function isActiveLink(path) {
  return route.path === path;
}

function changeLocale(code) {
  locale.value = code;
  localStorage.setItem('locale', code);
  showLangDropdown.value = false;
  // Set RTL for Arabic, Hebrew, Urdu
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
