<template>
  <div class="space-y-4">
    <!-- Welcome Bonus Banner -->
    <div class="promo-banner">
      <div class="p-5">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-8 h-8 bg-accent-gold/20 rounded-full flex items-center justify-center">
            <svg class="w-5 h-5 text-accent-gold" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20 6h-2.18c.11-.31.18-.65.18-1a3 3 0 00-5.17-2.06L12 4l-1-1.06A3 3 0 005.82 5c0 .35.07.69.18 1H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-5-2a1 1 0 110 2 1 1 0 010-2zM9 4a1 1 0 110 2 1 1 0 010-2zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
            </svg>
          </div>
          <span class="text-xs font-bold text-accent-gold uppercase tracking-wider">{{ $t('promo.welcome') }}</span>
        </div>
        <h3 class="text-lg font-bold text-white mb-1">100% {{ $t('nav.deposit') }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ $t('promo.welcomeDesc') }}</p>
        <button class="w-full bg-accent-gold hover:bg-yellow-400 text-dark-800 font-bold py-2.5 rounded-lg text-sm transition-colors">
          {{ $t('promo.claimNow') }}
        </button>
      </div>
    </div>

    <!-- Free Bet Banner -->
    <div class="promo-banner bg-gradient-to-r from-dark-600 to-primary-900/60">
      <div class="p-5">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-8 h-8 bg-primary-500/20 rounded-full flex items-center justify-center">
            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <span class="text-xs font-bold text-primary-400 uppercase tracking-wider">{{ $t('promo.freeBet') }}</span>
        </div>
        <h3 class="text-lg font-bold text-white mb-1">$50 {{ $t('promo.freeBet') }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ $t('promo.freeBetDesc') }}</p>
        <button class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-lg text-sm transition-colors">
          {{ $t('promo.claimNow') }}
        </button>
      </div>
    </div>

    <!-- Live Stats Widget -->
    <div class="bg-dark-500 rounded-xl border border-dark-300/30 p-4">
      <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Live Statistics</h4>
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-xs text-gray-400">Active Bets</span>
          <span class="text-sm font-bold text-primary-400">12,847</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-xs text-gray-400">Live Events</span>
          <span class="text-sm font-bold text-accent-gold">234</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-xs text-gray-400">Won Today</span>
          <span class="text-sm font-bold text-green-400">$1.2M</span>
        </div>
      </div>
    </div>

    <!-- Top Winners -->
    <div class="bg-dark-500 rounded-xl border border-dark-300/30 p-4">
      <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Top Winners</h4>
      <div class="space-y-2">
        <div v-for="(winner, i) in topWinners" :key="i" class="flex items-center justify-between py-1.5">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-full bg-dark-300 flex items-center justify-center text-[10px] font-bold text-gray-400">
              {{ winner.initials }}
            </div>
            <span class="text-xs text-gray-300">{{ winner.name }}</span>
          </div>
          <span class="text-xs font-bold text-accent-gold">+{{ winner.amount }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const topWinners = ref([
  { name: 'Alex K.', initials: 'AK', amount: '$4,230' },
  { name: 'Maria S.', initials: 'MS', amount: '$2,850' },
  { name: 'John D.', initials: 'JD', amount: '$1,920' },
  { name: 'Elena P.', initials: 'EP', amount: '$1,540' },
  { name: 'Marco R.', initials: 'MR', amount: '$1,180' },
]);
</script>
