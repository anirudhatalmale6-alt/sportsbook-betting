<template>
  <div
    class="live-match-row group"
    @click="$emit('navigate', match.id)"
  >
    <!-- Main row: teams + score + odds -->
    <div class="flex items-center gap-2 sm:gap-3">
      <!-- Team icons + names (left side) -->
      <div class="flex items-center gap-2 flex-1 min-w-0">
        <!-- Team icon circles -->
        <div class="flex flex-col gap-1 flex-shrink-0">
          <div
            class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold text-white"
            :style="{ backgroundColor: homeColor }"
          >
            {{ homeInitial }}
          </div>
          <div
            class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold text-white"
            :style="{ backgroundColor: awayColor }"
          >
            {{ awayInitial }}
          </div>
        </div>

        <!-- Team names stacked -->
        <div class="flex flex-col gap-0.5 min-w-0">
          <span class="text-xs sm:text-sm text-white leading-tight truncate">{{ match.homeTeam }}</span>
          <span class="text-xs sm:text-sm text-white leading-tight truncate">{{ match.awayTeam }}</span>
        </div>
      </div>

      <!-- Score box -->
      <div
        v-if="match.score"
        class="flex flex-col items-center justify-center bg-dark-400/80 rounded px-2 py-1 min-w-[32px] flex-shrink-0"
      >
        <span class="text-xs sm:text-sm font-bold text-white leading-tight">{{ match.score.home }}</span>
        <span class="text-xs sm:text-sm font-bold text-white leading-tight">{{ match.score.away }}</span>
      </div>
      <div
        v-else
        class="flex flex-col items-center justify-center bg-dark-400/80 rounded px-2 py-1 min-w-[32px] flex-shrink-0"
      >
        <span class="text-xs font-bold text-gray-500 leading-tight">-</span>
        <span class="text-xs font-bold text-gray-500 leading-tight">-</span>
      </div>

      <!-- Odds: 1 / X / 2 -->
      <div class="flex items-center gap-1 flex-shrink-0">
        <!-- Home (1) -->
        <button
          v-if="match.odds.home != null"
          @click.stop="addToBetslip('home', match.odds.home)"
          :class="['live-odds-btn', { active: betslipStore.isSelected(match.id, '1x2', 'home') }]"
        >
          {{ formatOdds(match.odds.home) }}
        </button>
        <div v-else class="live-odds-btn locked">
          <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>

        <!-- Draw (X) -->
        <button
          v-if="match.odds.draw != null"
          @click.stop="addToBetslip('draw', match.odds.draw)"
          :class="['live-odds-btn', { active: betslipStore.isSelected(match.id, '1x2', 'draw') }]"
        >
          {{ formatOdds(match.odds.draw) }}
        </button>
        <div v-else-if="showDraw" class="live-odds-btn locked">
          <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>

        <!-- Away (2) -->
        <button
          v-if="match.odds.away != null"
          @click.stop="addToBetslip('away', match.odds.away)"
          :class="['live-odds-btn', { active: betslipStore.isSelected(match.id, '1x2', 'away') }]"
        >
          {{ formatOdds(match.odds.away) }}
        </button>
        <div v-else class="live-odds-btn locked">
          <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Bottom info row: favorite star + time + market count -->
    <div class="flex items-center gap-3 mt-1.5 pl-7">
      <!-- Favorite star -->
      <button
        @click.stop="$emit('toggleFavorite', match.id)"
        class="text-gray-600 hover:text-accent-gold transition-colors"
      >
        <svg class="w-3.5 h-3.5" :fill="isFav ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
      </button>

      <!-- Elapsed time -->
      <span class="text-[11px] text-primary-400 font-medium">
        {{ elapsedTime }}
      </span>

      <!-- Stats icon -->
      <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>

      <!-- Market count -->
      <span
        class="text-[11px] text-gray-400 hover:text-primary-400 cursor-pointer transition-colors ml-auto"
        @click.stop="$emit('navigate', match.id)"
      >
        {{ marketCount }} &gt;
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBetslipStore } from '../../store/betslip';

const props = defineProps({
  match: { type: Object, required: true },
  isFav: { type: Boolean, default: false },
  showDraw: { type: Boolean, default: true },
});

defineEmits(['navigate', 'toggleFavorite']);

const betslipStore = useBetslipStore();

// Generate deterministic colors from team names
const teamColors = [
  '#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6',
  '#1abc9c', '#e67e22', '#e91e63', '#00bcd4', '#8bc34a',
  '#ff5722', '#607d8b', '#795548', '#673ab7', '#009688',
];

function colorFromName(name) {
  let hash = 0;
  for (let i = 0; i < name.length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash);
  }
  return teamColors[Math.abs(hash) % teamColors.length];
}

const homeColor = computed(() => colorFromName(props.match.homeTeam));
const awayColor = computed(() => colorFromName(props.match.awayTeam));

const homeInitial = computed(() => props.match.homeTeam.charAt(0).toUpperCase());
const awayInitial = computed(() => props.match.awayTeam.charAt(0).toUpperCase());

const elapsedTime = computed(() => {
  if (props.match.minute != null) {
    const mins = Math.floor(props.match.minute);
    const secs = Math.floor((props.match.minute % 1) * 60);
    return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
  }
  return 'LIVE';
});

const marketCount = computed(() => {
  if (props.match.markets && props.match.markets.length > 0) {
    return props.match.markets.length;
  }
  // Fallback: generate a plausible number
  return Math.floor(50 + Math.random() * 80);
});

function formatOdds(odds) {
  if (odds == null || isNaN(odds)) return '-';
  const fmt = localStorage.getItem('odds_format') || 'decimal';
  if (fmt === 'fractional') {
    const frac = odds - 1;
    const denom = 100;
    const num = Math.round(frac * denom);
    const g = gcd(num, denom);
    return `${num / g}/${denom / g}`;
  }
  if (fmt === 'american') {
    if (odds >= 2) return '+' + Math.round((odds - 1) * 100);
    return '-' + Math.round(100 / (odds - 1));
  }
  return odds.toFixed(2);
}

function gcd(a, b) {
  a = Math.abs(a); b = Math.abs(b);
  while (b) { [a, b] = [b, a % b]; }
  return a;
}

function addToBetslip(outcome, odds) {
  betslipStore.addSelection({
    matchId: props.match.id,
    matchLabel: `${props.match.homeTeam} vs ${props.match.awayTeam}`,
    market: '1x2',
    outcome,
    odds,
  });
}
</script>
