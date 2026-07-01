<template>
  <div class="mb-6">
    <div class="flex items-center mb-3">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
        <svg class="w-4 h-4 text-accent-gold" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        {{ $t('match.featured') }}
      </h2>
    </div>

    <div
      ref="scrollContainer"
      class="flex gap-3 overflow-x-auto hide-scrollbar pb-2"
    >
      <div
        v-for="match in sportsStore.featuredMatches"
        :key="match.id"
        class="jilbet-featured-card"
      >
        <!-- Card body -->
        <div class="p-4 flex flex-col justify-end h-full">
          <!-- League badge -->
          <div class="mb-auto">
            <span class="text-[10px] text-gray-500 uppercase tracking-wider">{{ match.league }}</span>
            <span v-if="match.isLive" class="badge-live ml-2 text-[8px]">
              {{ match.minute ? match.minute + "'" : $t('match.live') }}
            </span>
          </div>

          <!-- Teams + Date row -->
          <div class="flex items-end justify-between mt-4 mb-3">
            <div class="flex flex-col gap-0.5">
              <span class="text-sm text-white font-medium leading-tight">{{ match.homeTeam }}</span>
              <span class="text-sm text-white font-medium leading-tight">{{ match.awayTeam }}</span>
            </div>
            <div class="text-right flex-shrink-0 ml-3">
              <div class="text-[11px] text-gray-400 leading-tight">{{ formatDateFull(match.date) }}</div>
              <div class="text-[11px] text-gray-400 leading-tight">{{ match.time }}</div>
            </div>
          </div>

          <!-- Odds bar -->
          <div class="flex items-center bg-dark-700/80 rounded-lg overflow-hidden">
            <button
              @click="addToBetslip(match, 'home', match.odds.home)"
              :class="['jilbet-odds-cell flex-1', { active: betslipStore.isSelected(match.id, '1x2', 'home') }]"
            >
              <span class="text-gray-400 text-[10px] mr-1">1</span>
              <span class="text-primary-400 text-sm font-semibold">{{ formatOdds(match.odds.home) }}</span>
            </button>
            <button
              v-if="match.odds.draw !== null"
              @click="addToBetslip(match, 'draw', match.odds.draw)"
              :class="['jilbet-odds-cell flex-1 border-x border-dark-400/30', { active: betslipStore.isSelected(match.id, '1x2', 'draw') }]"
            >
              <span class="text-gray-400 text-[10px] mr-1">X</span>
              <span class="text-primary-400 text-sm font-semibold">{{ formatOdds(match.odds.draw) }}</span>
            </button>
            <button
              @click="addToBetslip(match, 'away', match.odds.away)"
              :class="['jilbet-odds-cell flex-1', { active: betslipStore.isSelected(match.id, '1x2', 'away') }]"
            >
              <span class="text-gray-400 text-[10px] mr-1">2</span>
              <span class="text-primary-400 text-sm font-semibold">{{ formatOdds(match.odds.away) }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useSportsStore } from '../../store/sports';
import { useBetslipStore } from '../../store/betslip';

const sportsStore = useSportsStore();
const betslipStore = useBetslipStore();
const scrollContainer = ref(null);

function formatOdds(odds) {
  if (odds == null || isNaN(odds)) return '-';
  return odds.toFixed(2);
}

function formatDateFull(dateStr) {
  const date = new Date(dateStr);
  const days = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
  const months = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
  const today = new Date();
  if (date.toDateString() === today.toDateString()) return 'Heute';
  return `${days[date.getDay()]}, ${String(date.getDate()).padStart(2, '0')} ${months[date.getMonth()]}`;
}

function addToBetslip(match, outcome, odds) {
  betslipStore.addSelection({
    matchId: match.id,
    matchLabel: `${match.homeTeam} vs ${match.awayTeam}`,
    market: '1x2',
    outcome,
    odds,
  });
}
</script>
