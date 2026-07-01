<template>
  <div>
    <!-- Time filter tabs -->
    <div class="flex items-center gap-2 mb-4">
      <button
        v-for="filter in timeFilters"
        :key="filter.value"
        @click="sportsStore.setTimeFilter(filter.value)"
        :class="['time-tab', { active: sportsStore.selectedTimeFilter === filter.value }]"
      >
        {{ filter.label }}
      </button>
    </div>

    <!-- Match groups by country/league -->
    <div class="space-y-2">
      <div
        v-for="group in sportsStore.matchesByCountry"
        :key="`${group.country}-${group.league}`"
        class="match-card"
      >
        <!-- Country/League header -->
        <div class="country-header">
          <span class="text-base">{{ group.flag }}</span>
          <span>{{ group.country }}</span>
          <span class="text-gray-500">-</span>
          <span class="text-gray-300 normal-case">{{ group.league }}</span>
          <span class="ml-auto text-gray-500">{{ group.matches.length }}</span>
        </div>

        <!-- Match rows -->
        <div class="divide-y divide-dark-400/20">
          <div
            v-for="match in group.matches"
            :key="match.id"
            class="flex items-center gap-3 px-3 py-2.5 hover:bg-dark-400/30 transition-colors"
          >
            <!-- Time / Live indicator -->
            <div class="w-12 flex-shrink-0 text-center">
              <template v-if="match.isLive">
                <span class="badge-live text-[8px]">
                  {{ match.minute ? match.minute + "'" : '' }}
                </span>
              </template>
              <template v-else>
                <div class="text-[11px] text-gray-500 leading-tight">
                  <div>{{ formatTime(match.time) }}</div>
                  <div class="text-[9px] text-gray-600">{{ formatShortDate(match.date) }}</div>
                </div>
              </template>
            </div>

            <!-- Teams -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-1.5 mb-0.5">
                <span class="text-xs text-white truncate">{{ match.homeTeam }}</span>
                <template v-if="match.isLive && match.score">
                  <span class="text-xs font-bold text-white ml-auto">{{ match.score.home }}</span>
                </template>
              </div>
              <div class="flex items-center gap-1.5">
                <span class="text-xs text-white truncate">{{ match.awayTeam }}</span>
                <template v-if="match.isLive && match.score">
                  <span class="text-xs font-bold text-white ml-auto">{{ match.score.away }}</span>
                </template>
              </div>
            </div>

            <!-- Odds -->
            <div class="flex gap-1.5 flex-shrink-0">
              <OddsButton
                :label="$t('match.home')"
                :odds="match.odds.home"
                :active="betslipStore.isSelected(match.id, '1x2', 'home')"
                @click="addToBetslip(match, 'home', match.odds.home)"
              />
              <OddsButton
                v-if="match.odds.draw !== null"
                :label="$t('match.draw')"
                :odds="match.odds.draw"
                :active="betslipStore.isSelected(match.id, '1x2', 'draw')"
                @click="addToBetslip(match, 'draw', match.odds.draw)"
              />
              <OddsButton
                :label="$t('match.away')"
                :odds="match.odds.away"
                :active="betslipStore.isSelected(match.id, '1x2', 'away')"
                @click="addToBetslip(match, 'away', match.odds.away)"
              />
            </div>

            <!-- More markets -->
            <button class="hidden sm:flex items-center gap-1 text-[10px] text-gray-500 hover:text-primary-400 transition-colors flex-shrink-0">
              <span>+42</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="sportsStore.matchesByCountry.length === 0" class="text-center py-12">
        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/>
        </svg>
        <p class="text-sm text-gray-500">No matches found</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useSportsStore } from '../../store/sports';
import { useBetslipStore } from '../../store/betslip';
import OddsButton from './OddsButton.vue';

const { t } = useI18n();
const sportsStore = useSportsStore();
const betslipStore = useBetslipStore();

const timeFilters = computed(() => [
  { value: 'today', label: t('match.today') },
  { value: '24h', label: t('match.hours24') },
  { value: '48h', label: t('match.hours48') },
  { value: 'all', label: t('match.allMatches') },
]);

function formatTime(time) {
  return time;
}

function formatShortDate(dateStr) {
  const date = new Date(dateStr);
  const today = new Date();
  if (date.toDateString() === today.toDateString()) return 'Today';
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
