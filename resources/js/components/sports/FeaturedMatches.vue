<template>
  <div class="mb-6">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
        <svg class="w-4 h-4 text-accent-gold" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        {{ $t('match.featured') }}
      </h2>
      <div class="flex gap-1">
        <button
          @click="scrollLeft"
          class="p-1.5 rounded-lg bg-dark-400 hover:bg-dark-300 transition-colors"
        >
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <button
          @click="scrollRight"
          class="p-1.5 rounded-lg bg-dark-400 hover:bg-dark-300 transition-colors"
        >
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>
    </div>

    <div
      ref="scrollContainer"
      class="flex gap-3 overflow-x-auto hide-scrollbar snap-x snap-mandatory pb-2"
    >
      <div
        v-for="match in sportsStore.featuredMatches"
        :key="match.id"
        class="featured-card"
      >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 pt-3 pb-2">
          <div class="flex items-center gap-1.5">
            <span class="text-xs text-gray-500">{{ match.league }}</span>
          </div>
          <span v-if="match.isLive" class="badge-live">
            {{ match.minute ? match.minute + "'" : $t('match.live') }}
          </span>
          <span v-else class="text-[10px] text-gray-500">
            {{ formatDate(match.date) }} {{ match.time }}
          </span>
        </div>

        <!-- Teams -->
        <div class="px-4 pb-3">
          <div class="flex items-center justify-between mb-3">
            <!-- Home team -->
            <div class="flex flex-col items-center gap-1.5 flex-1">
              <div class="w-10 h-10 bg-dark-400 rounded-full flex items-center justify-center text-lg">
                {{ match.homeFlag }}
              </div>
              <span class="text-xs text-white font-medium text-center leading-tight">{{ match.homeTeam }}</span>
              <span v-if="match.score" class="text-lg font-bold text-white">{{ match.score.home }}</span>
            </div>

            <!-- VS / Time -->
            <div class="flex flex-col items-center px-3">
              <span v-if="match.score" class="text-xs text-gray-500 font-medium">-</span>
              <span v-else class="text-xs text-gray-500 font-medium">{{ $t('match.vs') }}</span>
            </div>

            <!-- Away team -->
            <div class="flex flex-col items-center gap-1.5 flex-1">
              <div class="w-10 h-10 bg-dark-400 rounded-full flex items-center justify-center text-lg">
                {{ match.awayFlag }}
              </div>
              <span class="text-xs text-white font-medium text-center leading-tight">{{ match.awayTeam }}</span>
              <span v-if="match.score" class="text-lg font-bold text-white">{{ match.score.away }}</span>
            </div>
          </div>

          <!-- Odds row -->
          <div class="flex gap-2">
            <OddsButton
              :label="$t('match.home')"
              :odds="match.odds.home"
              :active="betslipStore.isSelected(match.id, '1x2', 'home')"
              @click="addToBetslip(match, 'home', match.odds.home)"
              class="flex-1"
            />
            <OddsButton
              v-if="match.odds.draw !== null"
              :label="$t('match.draw')"
              :odds="match.odds.draw"
              :active="betslipStore.isSelected(match.id, '1x2', 'draw')"
              @click="addToBetslip(match, 'draw', match.odds.draw)"
              class="flex-1"
            />
            <OddsButton
              :label="$t('match.away')"
              :odds="match.odds.away"
              :active="betslipStore.isSelected(match.id, '1x2', 'away')"
              @click="addToBetslip(match, 'away', match.odds.away)"
              class="flex-1"
            />
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
import OddsButton from './OddsButton.vue';

const sportsStore = useSportsStore();
const betslipStore = useBetslipStore();
const scrollContainer = ref(null);

function scrollLeft() {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({ left: -300, behavior: 'smooth' });
  }
}

function scrollRight() {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({ left: 300, behavior: 'smooth' });
  }
}

function formatDate(dateStr) {
  const date = new Date(dateStr);
  const today = new Date();
  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);

  if (date.toDateString() === today.toDateString()) return 'Today';
  if (date.toDateString() === tomorrow.toDateString()) return 'Tomorrow';
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
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
