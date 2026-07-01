<template>
  <div class="max-w-[1440px] mx-auto">
    <!-- Top tabs bar: A-Z | Live | Search | Bets -->
    <div class="flex items-center border-b border-dark-400/30 px-3 sm:px-4">
      <button
        @click="activeTab = 'az'"
        :class="['live-top-tab', { active: activeTab === 'az' }]"
      >
        A-Z
      </button>
      <button
        @click="activeTab = 'live'"
        :class="['live-top-tab', { active: activeTab === 'live' }]"
      >
        {{ $t('match.live') }}
      </button>
      <button class="p-2 ml-auto text-gray-400 hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </button>
      <button class="px-3 py-2 text-xs text-gray-400 hover:text-white transition-colors">
        {{ $t('nav.wetten') }}
      </button>
    </div>

    <!-- Sport selector: horizontal scroll with icons + match counts -->
    <div class="border-b border-dark-400/20 px-2 sm:px-3">
      <div class="flex gap-1 overflow-x-auto hide-scrollbar py-2">
        <!-- Favorites star -->
        <button
          @click="selectLiveSport('favorites')"
          :class="['live-sport-pill', { active: selectedLiveSport === 'favorites' }]"
        >
          <svg class="w-4 h-4" :fill="selectedLiveSport === 'favorites' ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
        </button>

        <!-- Sport pills with counts -->
        <button
          v-for="sport in liveSportsWithCounts"
          :key="sport.id"
          @click="selectLiveSport(sport.id)"
          :class="['live-sport-pill', { active: selectedLiveSport === sport.id }]"
        >
          <span class="text-base leading-none">{{ sport.icon }}</span>
          <span class="text-[10px] font-medium mt-0.5">{{ sport.liveCount }}</span>
        </button>
      </div>
    </div>

    <div class="flex">
      <!-- Left Sidebar (desktop) -->
      <aside class="hidden lg:block w-56 xl:w-60 flex-shrink-0 bg-dark-700 border-r border-dark-400/20 min-h-[calc(100vh-48px)] sticky top-12 h-[calc(100vh-48px)] overflow-y-auto">
        <Sidebar />
      </aside>

      <!-- Main content -->
      <div class="flex-1 min-w-0 px-3 sm:px-4 lg:px-6 py-4">
        <!-- Sport heading + filter row -->
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">
              {{ currentSportLabel }}
            </h2>
            <div class="relative mt-1" ref="sortDropdownRef">
              <button
                @click="showSortDropdown = !showSortDropdown"
                class="flex items-center gap-1 text-xs text-gray-400 hover:text-gray-300 transition-colors"
              >
                {{ sortOptions[selectedSort] }}
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <div
                v-if="showSortDropdown"
                class="absolute left-0 top-full mt-1 bg-dark-500 border border-dark-300/40 rounded-lg shadow-xl py-1 min-w-[150px] z-30"
              >
                <button
                  v-for="(label, key) in sortOptions"
                  :key="key"
                  @click="selectedSort = key; showSortDropdown = false"
                  :class="['w-full text-left px-3 py-2 text-xs hover:bg-dark-400 transition-colors', selectedSort === key ? 'text-primary-400' : 'text-gray-300']"
                >
                  {{ label }}
                </button>
              </div>
            </div>
          </div>

          <!-- Market type selector (Final result dropdown) -->
          <div class="relative" ref="marketDropdownRef">
            <button
              @click="showMarketDropdown = !showMarketDropdown"
              class="flex items-center gap-1.5 px-3 py-1.5 bg-dark-500 border border-dark-300/30 rounded-lg text-xs text-gray-300 hover:border-dark-200/50 transition-colors"
            >
              {{ marketTypes[selectedMarket] }}
              <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div
              v-if="showMarketDropdown"
              class="absolute right-0 top-full mt-1 bg-dark-500 border border-dark-300/40 rounded-lg shadow-xl py-1 min-w-[160px] z-30"
            >
              <button
                v-for="(label, key) in marketTypes"
                :key="key"
                @click="selectedMarket = key; showMarketDropdown = false"
                :class="['w-full text-left px-3 py-2 text-xs hover:bg-dark-400 transition-colors', selectedMarket === key ? 'text-primary-400' : 'text-gray-300']"
              >
                {{ label }}
              </button>
            </div>
          </div>
        </div>

        <!-- Loading state -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-16">
          <div class="w-8 h-8 border-2 border-primary-500/30 border-t-primary-500 rounded-full animate-spin mb-3"></div>
          <span class="text-sm text-gray-500">Loading live matches...</span>
        </div>

        <!-- Live matches grouped by league -->
        <div v-else-if="groupedMatches.length > 0" class="space-y-1">
          <div
            v-for="group in groupedMatches"
            :key="group.key"
          >
            <!-- League header -->
            <div class="flex items-center gap-2 px-2 py-2 bg-dark-600/60 rounded-t border-b border-dark-400/20">
              <span class="text-sm">{{ group.flag }}</span>
              <span class="text-xs font-semibold text-gray-300 flex-1 truncate">{{ group.country }} - {{ group.league }}</span>
              <!-- Column headers: 1 X 2 -->
              <div class="flex items-center gap-1 flex-shrink-0 mr-1">
                <span class="text-[10px] text-gray-500 font-bold w-12 sm:w-14 text-center">1</span>
                <span class="text-[10px] text-gray-500 font-bold w-12 sm:w-14 text-center">X</span>
                <span class="text-[10px] text-gray-500 font-bold w-12 sm:w-14 text-center">2</span>
              </div>
            </div>

            <!-- Match rows -->
            <LiveMatchRow
              v-for="match in group.matches"
              :key="match.id"
              :match="match"
              :isFav="sportsStore.isFavorite(match.id)"
              :showDraw="match.sport === 'football' || match.sport === 'handball'"
              @navigate="navigateToMatch"
              @toggle-favorite="sportsStore.toggleFavorite"
            />
          </div>
        </div>

        <!-- Empty state -->
        <div v-else class="text-center py-16">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-dark-500 flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
          </div>
          <p class="text-sm text-gray-500 mb-1">No live matches at the moment</p>
          <p class="text-xs text-gray-600">Check back soon for upcoming live events</p>
        </div>
      </div>

      <!-- Right Sidebar (desktop) -->
      <aside class="hidden xl:block w-72 flex-shrink-0 p-4 sticky top-12 h-[calc(100vh-48px)] overflow-y-auto">
        <RightSidebar />
      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useSportsStore } from '../../store/sports';
import Sidebar from '../../components/layout/Sidebar.vue';
import RightSidebar from '../../components/layout/RightSidebar.vue';
import LiveMatchRow from '../../components/sports/LiveMatchRow.vue';

const { t, te } = useI18n();
const router = useRouter();
const sportsStore = useSportsStore();

const loading = ref(true);
const activeTab = ref('live');
const selectedLiveSport = ref('all');
const selectedSort = ref('default');
const selectedMarket = ref('1x2');
const showSortDropdown = ref(false);
const showMarketDropdown = ref(false);
const sortDropdownRef = ref(null);
const marketDropdownRef = ref(null);
let refreshInterval = null;

const sortOptions = {
  default: 'Default selection',
  time: 'By time',
  league: 'By league',
};

const marketTypes = {
  '1x2': 'Final result',
  'ou': 'Over/Under',
  'btts': 'Both teams to score',
  'dc': 'Double chance',
  'ht': 'Half-time result',
};

// Compute live sports with their live match counts
const liveSportsWithCounts = computed(() => {
  const allLive = allLiveMatches.value;
  const sportCounts = {};
  allLive.forEach(m => {
    const sid = m.sport || 'football';
    sportCounts[sid] = (sportCounts[sid] || 0) + 1;
  });

  return sportsStore.sports
    .map(s => ({
      ...s,
      liveCount: sportCounts[s.id] || 0,
    }))
    .filter(s => s.liveCount > 0);
});

// All live matches (from store or fallback)
const allLiveMatches = computed(() => {
  if (sportsStore.liveMatches.length > 0) {
    return sportsStore.liveMatches;
  }
  // Fallback demo data
  return getFallbackLiveMatches();
});

// Filtered live matches based on selected sport
const filteredLiveMatches = computed(() => {
  let result = allLiveMatches.value;

  if (selectedLiveSport.value === 'favorites') {
    result = result.filter(m => sportsStore.isFavorite(m.id));
  } else if (selectedLiveSport.value !== 'all') {
    result = result.filter(m => m.sport === selectedLiveSport.value);
  }

  return result;
});

// Grouped by league
const groupedMatches = computed(() => {
  const groups = {};
  filteredLiveMatches.value.forEach(match => {
    const key = `${match.country} - ${match.league}`;
    if (!groups[key]) {
      groups[key] = {
        key,
        country: match.country || 'International',
        league: match.league || 'Unknown',
        flag: match.countryFlag || '🌍',
        matches: [],
      };
    }
    groups[key].matches.push(match);
  });

  let result = Object.values(groups);

  // Sort groups
  if (selectedSort.value === 'league') {
    result.sort((a, b) => a.league.localeCompare(b.league));
  }

  return result;
});

// Current sport label
const currentSportLabel = computed(() => {
  if (selectedLiveSport.value === 'all' || selectedLiveSport.value === 'favorites') {
    const key = 'sports.allSports';
    return te(key) ? t(key) : 'All Sports';
  }
  const sport = sportsStore.sports.find(s => s.id === selectedLiveSport.value);
  if (sport) {
    const key = `sports.${sport.id}`;
    return te(key) ? t(key) : sport.name;
  }
  return 'All Sports';
});

function selectLiveSport(sportId) {
  selectedLiveSport.value = sportId;
}

function navigateToMatch(matchId) {
  // Placeholder for match detail route
  router.push(`/match/${matchId}`);
}

function handleClickOutside(e) {
  if (sortDropdownRef.value && !sortDropdownRef.value.contains(e.target)) {
    showSortDropdown.value = false;
  }
  if (marketDropdownRef.value && !marketDropdownRef.value.contains(e.target)) {
    showMarketDropdown.value = false;
  }
}

function getFallbackLiveMatches() {
  return [
    { id: 'l1', homeTeam: 'Panathinaikos', awayTeam: 'Olympiacos', league: 'Super League', country: 'Greece', countryFlag: '🇬🇷', odds: { home: 2.50, draw: 3.20, away: 2.80 }, isLive: true, minute: 36, score: { home: 1, away: 0 }, sport: 'football', markets: new Array(102) },
    { id: 'l2', homeTeam: 'AEK Athens', awayTeam: 'PAOK', league: 'Super League', country: 'Greece', countryFlag: '🇬🇷', odds: { home: 1.85, draw: 3.40, away: 4.20 }, isLive: true, minute: 22, score: { home: 0, away: 0 }, sport: 'football', markets: new Array(98) },
    { id: 'l3', homeTeam: 'Real Madrid', awayTeam: 'Atletico Madrid', league: 'La Liga', country: 'Spain', countryFlag: '🇪🇸', odds: { home: 1.30, draw: 4.33, away: 9.00 }, isLive: true, minute: 67, score: { home: 2, away: 0 }, sport: 'football', markets: new Array(115) },
    { id: 'l4', homeTeam: 'FC Barcelona', awayTeam: 'Valencia', league: 'La Liga', country: 'Spain', countryFlag: '🇪🇸', odds: { home: 1.45, draw: 4.00, away: 6.50 }, isLive: true, minute: 51, score: { home: 1, away: 1 }, sport: 'football', markets: new Array(108) },
    { id: 'l5', homeTeam: 'Manchester City', awayTeam: 'Liverpool', league: 'Premier League', country: 'England', countryFlag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿', odds: { home: 1.95, draw: 3.60, away: 3.50 }, isLive: true, minute: 12, score: { home: 0, away: 1 }, sport: 'football', markets: new Array(124) },
    { id: 'l6', homeTeam: 'Arsenal', awayTeam: 'Tottenham', league: 'Premier League', country: 'England', countryFlag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿', odds: { home: 1.60, draw: 3.80, away: 5.20 }, isLive: true, minute: 78, score: { home: 3, away: 1 }, sport: 'football', markets: new Array(89) },
    { id: 'l7', homeTeam: 'Bayern Munich', awayTeam: 'Borussia Dortmund', league: 'Bundesliga', country: 'Germany', countryFlag: '🇩🇪', odds: { home: 1.65, draw: 4.00, away: 4.75 }, isLive: true, minute: 45, score: { home: 1, away: 1 }, sport: 'football', markets: new Array(112) },
    { id: 'l8', homeTeam: 'Galatasaray', awayTeam: 'Fenerbahce', league: 'Super Lig', country: 'Turkey', countryFlag: '🇹🇷', odds: { home: 2.00, draw: 3.40, away: 3.50 }, isLive: true, minute: 33, score: { home: 0, away: 0 }, sport: 'football', markets: new Array(95) },
    { id: 'l9', homeTeam: 'LA Lakers', awayTeam: 'Boston Celtics', league: 'NBA', country: 'USA', countryFlag: '🇺🇸', odds: { home: 1.75, draw: null, away: 2.10 }, isLive: true, minute: 28, score: { home: 54, away: 48 }, sport: 'basketball', markets: new Array(67) },
    { id: 'l10', homeTeam: 'Golden State Warriors', awayTeam: 'Miami Heat', league: 'NBA', country: 'USA', countryFlag: '🇺🇸', odds: { home: 1.90, draw: null, away: 1.95 }, isLive: true, minute: 15, score: { home: 22, away: 25 }, sport: 'basketball', markets: new Array(72) },
    { id: 'l11', homeTeam: 'Djokovic N.', awayTeam: 'Alcaraz C.', league: 'Wimbledon', country: 'International', countryFlag: '🌍', odds: { home: 1.55, draw: null, away: 2.40 }, isLive: true, minute: 90, score: { home: 2, away: 1 }, sport: 'tennis', markets: new Array(45) },
    { id: 'l12', homeTeam: 'Juventus', awayTeam: 'AC Milan', league: 'Serie A', country: 'Italy', countryFlag: '🇮🇹', odds: { home: 2.20, draw: 3.10, away: 3.30 }, isLive: true, minute: 5, score: { home: 0, away: 0 }, sport: 'football', markets: new Array(110) },
  ];
}

onMounted(async () => {
  loading.value = true;
  await sportsStore.fetchSports();
  await sportsStore.fetchLiveMatches();
  loading.value = false;

  // Auto-refresh live data every 30 seconds
  refreshInterval = setInterval(async () => {
    await sportsStore.fetchLiveMatches();
  }, 30000);

  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  if (refreshInterval) clearInterval(refreshInterval);
  document.removeEventListener('click', handleClickOutside);
});
</script>
