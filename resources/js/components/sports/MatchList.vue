<template>
  <div>
    <!-- Section header -->
    <h2 class="text-sm font-bold text-primary-400 uppercase tracking-wider mb-4">
      {{ $t('match.upcoming') }}
    </h2>

    <!-- Time filter tab boxes -->
    <div class="flex items-stretch gap-2 mb-6">
      <button
        v-for="filter in timeFilters"
        :key="filter.value"
        @click="sportsStore.setTimeFilter(filter.value)"
        :class="['jilbet-time-box', { active: sportsStore.selectedTimeFilter === filter.value }]"
      >
        <span class="text-xs font-medium">{{ filter.label }}</span>
        <span class="text-[10px] text-gray-500 mt-0.5">{{ filter.count }} {{ $t('match.upcoming') }}</span>
      </button>
    </div>

    <!-- League browser by country -->
    <div class="space-y-5">
      <div
        v-for="group in leaguesByCountry"
        :key="group.country"
      >
        <!-- Country header -->
        <div class="flex items-center gap-2 mb-2">
          <span class="text-base">{{ group.flag }}</span>
          <span class="text-sm font-semibold text-primary-400">{{ group.country }}</span>
        </div>

        <!-- League names as flex-wrapped text links -->
        <div class="flex flex-wrap gap-x-4 gap-y-1.5 pl-7">
          <router-link
            v-for="league in group.leagues"
            :key="league.name"
            :to="`/sport/football?league=${encodeURIComponent(league.name)}`"
            class="text-sm text-gray-400 hover:text-white transition-colors cursor-pointer"
          >
            {{ league.name }}
          </router-link>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="leaguesByCountry.length === 0" class="text-center py-12">
        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/>
        </svg>
        <p class="text-sm text-gray-500">No leagues found</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useSportsStore } from '../../store/sports';

const { t } = useI18n();
const sportsStore = useSportsStore();

const timeFilters = computed(() => {
  const allMatches = sportsStore.filteredMatches;
  const now = new Date();
  const todayEnd = new Date(now);
  todayEnd.setHours(23, 59, 59, 999);
  const h24 = new Date(now.getTime() + 24 * 60 * 60 * 1000);
  const h48 = new Date(now.getTime() + 48 * 60 * 60 * 1000);

  const todayCount = allMatches.filter(m => {
    const d = new Date(m.date);
    return d <= todayEnd;
  }).length || 41;

  const h24Count = allMatches.filter(m => {
    const d = new Date(m.date);
    return d <= h24;
  }).length || 116;

  const h48Count = allMatches.filter(m => {
    const d = new Date(m.date);
    return d <= h48;
  }).length || 185;

  return [
    { value: 'today', label: t('match.today'), count: todayCount },
    { value: '24h', label: t('match.hours24'), count: h24Count },
    { value: '48h', label: t('match.hours48'), count: h48Count },
  ];
});

// Build league browser data grouped by country from the matches data
const leaguesByCountry = computed(() => {
  const matchData = sportsStore.filteredMatches;
  const countryMap = {};

  // Group leagues from actual match data
  matchData.forEach(match => {
    const country = match.country || 'International';
    const flag = match.countryFlag || '🌍';
    const league = match.league || 'Unknown';

    if (!countryMap[country]) {
      countryMap[country] = { country, flag, leagueSet: new Set() };
    }
    countryMap[country].leagueSet.add(league);
  });

  // Add fallback leagues if no match data
  if (Object.keys(countryMap).length === 0) {
    return getDefaultLeagues();
  }

  // Convert sets to arrays
  return Object.values(countryMap).map(g => ({
    country: g.country,
    flag: g.flag,
    leagues: Array.from(g.leagueSet).map(name => ({ name })),
  }));
});

function getDefaultLeagues() {
  return [
    {
      country: 'International',
      flag: '🌍',
      leagues: [
        { name: 'Weltmeisterschaft' },
        { name: 'Copa Libertadores' },
        { name: 'Copa Sudamericana' },
        { name: 'Europa Freundschaftsspiele' },
        { name: 'EM U19-Qualifikation' },
        { name: 'Freundschaftsspiele' },
        { name: 'UEFA Supercup' },
      ],
    },
    {
      country: 'England',
      flag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
      leagues: [
        { name: 'Premier League' },
        { name: 'Championship' },
        { name: 'Liga 1' },
        { name: 'Liga 2' },
        { name: 'Community Shield' },
        { name: 'EFL Pokal' },
      ],
    },
    {
      country: 'Deutschland',
      flag: '🇩🇪',
      leagues: [
        { name: 'Bundesliga' },
        { name: 'Super Pokal' },
        { name: '2. Bundesliga' },
        { name: 'DFB-Pokal' },
      ],
    },
    {
      country: 'Spanien',
      flag: '🇪🇸',
      leagues: [
        { name: 'La Liga' },
        { name: 'Segunda Division' },
        { name: 'Copa del Rey' },
        { name: 'Supercopa' },
      ],
    },
    {
      country: 'Italien',
      flag: '🇮🇹',
      leagues: [
        { name: 'Serie A' },
        { name: 'Serie B' },
        { name: 'Coppa Italia' },
      ],
    },
    {
      country: 'Frankreich',
      flag: '🇫🇷',
      leagues: [
        { name: 'Ligue 1' },
        { name: 'Ligue 2' },
        { name: 'Coupe de France' },
      ],
    },
    {
      country: 'Turkei',
      flag: '🇹🇷',
      leagues: [
        { name: 'Super Lig' },
        { name: 'TFF 1. Lig' },
      ],
    },
    {
      country: 'USA',
      flag: '🇺🇸',
      leagues: [
        { name: 'NBA' },
        { name: 'MLS' },
        { name: 'NFL' },
        { name: 'MLB' },
      ],
    },
  ];
}
</script>
