<template>
  <!-- Overlay -->
  <div
    v-if="betslipStore.isOpen"
    class="fixed inset-0 bg-black/50 z-40"
    @click="betslipStore.toggleOpen()"
  ></div>

  <!-- Panel -->
  <div
    :class="['betslip-panel', { closed: !betslipStore.isOpen }]"
  >
    <!-- Header -->
    <div class="flex items-center justify-between px-4 py-3 bg-dark-700 border-b border-dark-400/30">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <h3 class="text-sm font-bold text-white">{{ $t('betslip.title') }}</h3>
        <span
          v-if="betslipStore.selectionCount > 0"
          class="bg-primary-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full"
        >
          {{ betslipStore.selectionCount }}
        </span>
      </div>
      <button
        @click="betslipStore.toggleOpen()"
        class="p-1.5 rounded-lg hover:bg-dark-500 transition-colors"
      >
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Bet type tabs -->
    <div class="flex border-b border-dark-400/30">
      <button
        @click="betslipStore.betType = 'single'"
        :class="[
          'flex-1 py-2.5 text-xs font-medium transition-colors border-b-2',
          betslipStore.betType === 'single'
            ? 'text-primary-400 border-primary-500 bg-dark-600'
            : 'text-gray-500 border-transparent hover:text-gray-300'
        ]"
      >
        {{ $t('betslip.single') }}
      </button>
      <button
        @click="betslipStore.betType = 'accumulator'"
        :class="[
          'flex-1 py-2.5 text-xs font-medium transition-colors border-b-2',
          betslipStore.betType === 'accumulator'
            ? 'text-primary-400 border-primary-500 bg-dark-600'
            : 'text-gray-500 border-transparent hover:text-gray-300'
        ]"
      >
        {{ $t('betslip.accumulator') }}
      </button>
    </div>

    <!-- Selections -->
    <div class="flex-1 overflow-y-auto" style="max-height: calc(100vh - 320px);">
      <!-- Empty state -->
      <div v-if="betslipStore.selectionCount === 0" class="flex flex-col items-center justify-center py-16 px-4">
        <svg class="w-16 h-16 text-dark-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-sm text-gray-500 text-center mb-1">{{ $t('betslip.empty') }}</p>
        <p class="text-xs text-gray-600 text-center">{{ $t('betslip.emptyHint') }}</p>
      </div>

      <!-- Selection items -->
      <div v-else class="divide-y divide-dark-400/20">
        <div
          v-for="(sel, index) in betslipStore.selections"
          :key="index"
          class="px-4 py-3"
        >
          <div class="flex items-start justify-between mb-1.5">
            <div class="flex-1 min-w-0">
              <p class="text-xs text-gray-400 truncate">{{ sel.matchLabel }}</p>
              <p class="text-sm text-white font-medium mt-0.5">
                {{ getOutcomeLabel(sel.outcome) }}
              </p>
            </div>
            <button
              @click="betslipStore.removeSelection(index)"
              class="ml-2 p-1 rounded hover:bg-dark-400 transition-colors flex-shrink-0"
            >
              <svg class="w-3.5 h-3.5 text-gray-500 hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-[10px] text-gray-500 uppercase">{{ sel.market }}</span>
            <span class="text-sm font-bold text-accent-gold">{{ sel.odds.toFixed(2) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer (shown when has selections) -->
    <div
      v-if="betslipStore.selectionCount > 0"
      class="absolute bottom-0 left-0 right-0 bg-dark-700 border-t border-dark-400/30 p-4"
    >
      <!-- Clear all -->
      <div class="flex justify-end mb-3">
        <button
          @click="betslipStore.clearAll()"
          class="text-xs text-gray-500 hover:text-red-400 transition-colors"
        >
          {{ $t('betslip.clearAll') }}
        </button>
      </div>

      <!-- Stake input -->
      <div class="mb-3">
        <label class="text-[10px] text-gray-500 uppercase tracking-wider mb-1 block">
          {{ $t('betslip.stake') }}
        </label>
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">$</span>
          <input
            v-model.number="betslipStore.stake"
            type="number"
            min="1"
            class="w-full bg-dark-500 border border-dark-300/40 rounded-lg pl-7 pr-3 py-2.5 text-sm text-white font-medium focus:outline-none focus:border-primary-500/50"
          />
        </div>
        <!-- Quick stake buttons -->
        <div class="flex gap-1.5 mt-2">
          <button
            v-for="amount in [5, 10, 25, 50, 100]"
            :key="amount"
            @click="betslipStore.stake = amount"
            :class="[
              'flex-1 py-1.5 rounded text-xs font-medium transition-colors',
              betslipStore.stake === amount
                ? 'bg-primary-600/30 text-primary-400 border border-primary-500/30'
                : 'bg-dark-500 text-gray-400 hover:bg-dark-400 border border-dark-300/20'
            ]"
          >
            ${{ amount }}
          </button>
        </div>
      </div>

      <!-- Totals -->
      <div class="space-y-1.5 mb-3">
        <div class="flex justify-between text-xs">
          <span class="text-gray-400">{{ $t('betslip.totalOdds') }}</span>
          <span class="font-bold text-white">{{ betslipStore.totalOdds.toFixed(2) }}</span>
        </div>
        <div class="flex justify-between text-xs">
          <span class="text-gray-400">{{ $t('betslip.potentialWin') }}</span>
          <span class="font-bold text-accent-gold text-sm">${{ betslipStore.potentialWinnings.toFixed(2) }}</span>
        </div>
      </div>

      <!-- Place bet button -->
      <button
        @click="betslipStore.placeBet()"
        class="btn-primary flex items-center justify-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ $t('betslip.placeBet') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { useBetslipStore } from '../../store/betslip';
import { useI18n } from 'vue-i18n';

const betslipStore = useBetslipStore();
const { t } = useI18n();

function getOutcomeLabel(outcome) {
  switch (outcome) {
    case 'home': return t('match.home') + ' - Home Win';
    case 'draw': return t('match.draw') + ' - Draw';
    case 'away': return t('match.away') + ' - Away Win';
    default: return outcome;
  }
}
</script>
