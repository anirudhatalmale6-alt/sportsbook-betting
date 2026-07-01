import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useSportsStore = defineStore('sports', () => {
  const selectedSport = ref('football');
  const selectedTimeFilter = ref('today');
  const searchQuery = ref('');
  const favorites = ref(JSON.parse(localStorage.getItem('sport_favorites') || '[]'));

  const sports = ref([
    { id: 'football', name: 'Football', icon: '⚽', count: 342 },
    { id: 'basketball', name: 'Basketball', icon: '🏀', count: 87 },
    { id: 'wm2026', name: 'WM 2026', icon: '🏆', count: 64 },
    { id: 'volleyball', name: 'Volleyball', icon: '🏐', count: 45 },
    { id: 'tennis', name: 'Tennis', icon: '🎾', count: 123 },
    { id: 'formula1', name: 'Formula 1', icon: '🏎️', count: 12 },
    { id: 'combat', name: 'Combat Sports', icon: '🥊', count: 28 },
    { id: 'tabletennis', name: 'Table Tennis', icon: '🏓', count: 56 },
    { id: 'netball', name: 'Netball', icon: '🤾', count: 18 },
    { id: 'handball', name: 'Handball', icon: '🤾‍♂️', count: 34 },
    { id: 'americanfootball', name: 'American Football', icon: '🏈', count: 42 },
    { id: 'baseball', name: 'Baseball', icon: '⚾', count: 38 },
  ]);

  const featuredMatches = ref([
    {
      id: 'f1',
      homeTeam: 'Real Madrid',
      awayTeam: 'FC Barcelona',
      homeFlag: '🇪🇸',
      awayFlag: '🇪🇸',
      league: 'La Liga',
      country: 'Spain',
      date: '2026-07-02',
      time: '21:00',
      odds: { home: 2.10, draw: 3.40, away: 3.25 },
      isLive: false,
      minute: null,
      score: null,
    },
    {
      id: 'f2',
      homeTeam: 'Manchester City',
      awayTeam: 'Liverpool FC',
      homeFlag: '🇬🇧',
      awayFlag: '🇬🇧',
      league: 'Premier League',
      country: 'England',
      date: '2026-07-02',
      time: '17:30',
      odds: { home: 1.95, draw: 3.60, away: 3.50 },
      isLive: true,
      minute: 67,
      score: { home: 2, away: 1 },
    },
    {
      id: 'f3',
      homeTeam: 'Bayern Munich',
      awayTeam: 'Borussia Dortmund',
      homeFlag: '🇩🇪',
      awayFlag: '🇩🇪',
      league: 'Bundesliga',
      country: 'Germany',
      date: '2026-07-03',
      time: '18:30',
      odds: { home: 1.65, draw: 4.00, away: 4.75 },
      isLive: false,
      minute: null,
      score: null,
    },
    {
      id: 'f4',
      homeTeam: 'Paris Saint-Germain',
      awayTeam: 'Olympique Marseille',
      homeFlag: '🇫🇷',
      awayFlag: '🇫🇷',
      league: 'Ligue 1',
      country: 'France',
      date: '2026-07-03',
      time: '21:00',
      odds: { home: 1.45, draw: 4.50, away: 6.00 },
      isLive: false,
      minute: null,
      score: null,
    },
    {
      id: 'f5',
      homeTeam: 'Juventus',
      awayTeam: 'AC Milan',
      homeFlag: '🇮🇹',
      awayFlag: '🇮🇹',
      league: 'Serie A',
      country: 'Italy',
      date: '2026-07-02',
      time: '20:45',
      odds: { home: 2.30, draw: 3.20, away: 2.90 },
      isLive: false,
      minute: null,
      score: null,
    },
  ]);

  const matches = ref([
    // England - Premier League
    {
      id: 'm1',
      homeTeam: 'Arsenal',
      awayTeam: 'Chelsea',
      league: 'Premier League',
      country: 'England',
      countryFlag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
      date: '2026-07-02',
      time: '15:00',
      odds: { home: 1.80, draw: 3.70, away: 4.20 },
      isLive: false,
      sport: 'football',
    },
    {
      id: 'm2',
      homeTeam: 'Tottenham',
      awayTeam: 'Manchester United',
      league: 'Premier League',
      country: 'England',
      countryFlag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
      date: '2026-07-02',
      time: '17:30',
      odds: { home: 2.40, draw: 3.30, away: 2.80 },
      isLive: false,
      sport: 'football',
    },
    {
      id: 'm3',
      homeTeam: 'Newcastle',
      awayTeam: 'Aston Villa',
      league: 'Premier League',
      country: 'England',
      countryFlag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
      date: '2026-07-02',
      time: '15:00',
      odds: { home: 1.90, draw: 3.50, away: 3.80 },
      isLive: false,
      sport: 'football',
    },
    // Spain - La Liga
    {
      id: 'm4',
      homeTeam: 'Atletico Madrid',
      awayTeam: 'Sevilla',
      league: 'La Liga',
      country: 'Spain',
      countryFlag: '🇪🇸',
      date: '2026-07-02',
      time: '19:00',
      odds: { home: 1.55, draw: 4.00, away: 5.50 },
      isLive: false,
      sport: 'football',
    },
    {
      id: 'm5',
      homeTeam: 'Real Sociedad',
      awayTeam: 'Valencia',
      league: 'La Liga',
      country: 'Spain',
      countryFlag: '🇪🇸',
      date: '2026-07-02',
      time: '21:00',
      odds: { home: 2.05, draw: 3.30, away: 3.40 },
      isLive: false,
      sport: 'football',
    },
    // Germany - Bundesliga
    {
      id: 'm6',
      homeTeam: 'RB Leipzig',
      awayTeam: 'Bayer Leverkusen',
      league: 'Bundesliga',
      country: 'Germany',
      countryFlag: '🇩🇪',
      date: '2026-07-02',
      time: '18:30',
      odds: { home: 2.60, draw: 3.40, away: 2.55 },
      isLive: true,
      minute: 34,
      score: { home: 1, away: 0 },
      sport: 'football',
    },
    {
      id: 'm7',
      homeTeam: 'Wolfsburg',
      awayTeam: 'Eintracht Frankfurt',
      league: 'Bundesliga',
      country: 'Germany',
      countryFlag: '🇩🇪',
      date: '2026-07-03',
      time: '15:30',
      odds: { home: 2.20, draw: 3.30, away: 3.10 },
      isLive: false,
      sport: 'football',
    },
    // Italy - Serie A
    {
      id: 'm8',
      homeTeam: 'Inter Milan',
      awayTeam: 'Napoli',
      league: 'Serie A',
      country: 'Italy',
      countryFlag: '🇮🇹',
      date: '2026-07-03',
      time: '20:45',
      odds: { home: 1.85, draw: 3.60, away: 3.90 },
      isLive: false,
      sport: 'football',
    },
    {
      id: 'm9',
      homeTeam: 'AS Roma',
      awayTeam: 'Lazio',
      league: 'Serie A',
      country: 'Italy',
      countryFlag: '🇮🇹',
      date: '2026-07-03',
      time: '18:00',
      odds: { home: 2.15, draw: 3.20, away: 3.30 },
      isLive: false,
      sport: 'football',
    },
    // France - Ligue 1
    {
      id: 'm10',
      homeTeam: 'Lyon',
      awayTeam: 'Monaco',
      league: 'Ligue 1',
      country: 'France',
      countryFlag: '🇫🇷',
      date: '2026-07-02',
      time: '21:00',
      odds: { home: 2.40, draw: 3.30, away: 2.80 },
      isLive: false,
      sport: 'football',
    },
    // Turkey - Super Lig
    {
      id: 'm11',
      homeTeam: 'Galatasaray',
      awayTeam: 'Fenerbahce',
      league: 'Super Lig',
      country: 'Turkey',
      countryFlag: '🇹🇷',
      date: '2026-07-02',
      time: '20:00',
      odds: { home: 2.00, draw: 3.40, away: 3.50 },
      isLive: false,
      sport: 'football',
    },
    // Basketball
    {
      id: 'm12',
      homeTeam: 'LA Lakers',
      awayTeam: 'Boston Celtics',
      league: 'NBA',
      country: 'USA',
      countryFlag: '🇺🇸',
      date: '2026-07-02',
      time: '02:00',
      odds: { home: 1.75, draw: null, away: 2.10 },
      isLive: false,
      sport: 'basketball',
    },
    {
      id: 'm13',
      homeTeam: 'Golden State Warriors',
      awayTeam: 'Miami Heat',
      league: 'NBA',
      country: 'USA',
      countryFlag: '🇺🇸',
      date: '2026-07-02',
      time: '04:30',
      odds: { home: 1.90, draw: null, away: 1.95 },
      isLive: false,
      sport: 'basketball',
    },
    // Tennis
    {
      id: 'm14',
      homeTeam: 'C. Alcaraz',
      awayTeam: 'J. Sinner',
      league: 'Wimbledon',
      country: 'International',
      countryFlag: '🎾',
      date: '2026-07-02',
      time: '14:00',
      odds: { home: 1.85, draw: null, away: 1.95 },
      isLive: true,
      minute: null,
      set: '2nd Set',
      score: { home: '6-4, 3-2', away: '4-6, 2-3' },
      sport: 'tennis',
    },
  ]);

  const filteredMatches = computed(() => {
    let result = matches.value;

    if (selectedSport.value !== 'all') {
      result = result.filter(m => m.sport === selectedSport.value);
    }

    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase();
      result = result.filter(m =>
        m.homeTeam.toLowerCase().includes(q) ||
        m.awayTeam.toLowerCase().includes(q) ||
        m.league.toLowerCase().includes(q)
      );
    }

    return result;
  });

  const matchesByCountry = computed(() => {
    const groups = {};
    filteredMatches.value.forEach(match => {
      const key = `${match.country} - ${match.league}`;
      if (!groups[key]) {
        groups[key] = {
          country: match.country,
          league: match.league,
          flag: match.countryFlag,
          matches: [],
        };
      }
      groups[key].matches.push(match);
    });
    return Object.values(groups);
  });

  function toggleFavorite(sportId) {
    const idx = favorites.value.indexOf(sportId);
    if (idx >= 0) {
      favorites.value.splice(idx, 1);
    } else {
      favorites.value.push(sportId);
    }
    localStorage.setItem('sport_favorites', JSON.stringify(favorites.value));
  }

  function isFavorite(sportId) {
    return favorites.value.includes(sportId);
  }

  function selectSport(sportId) {
    selectedSport.value = sportId;
  }

  function setTimeFilter(filter) {
    selectedTimeFilter.value = filter;
  }

  return {
    selectedSport,
    selectedTimeFilter,
    searchQuery,
    favorites,
    sports,
    featuredMatches,
    matches,
    filteredMatches,
    matchesByCountry,
    toggleFavorite,
    isFavorite,
    selectSport,
    setTimeFilter,
  };
});
