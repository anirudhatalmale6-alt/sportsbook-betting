import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

const API_BASE = '/api';

export const useSportsStore = defineStore('sports', () => {
  const selectedSport = ref('all');
  const selectedTimeFilter = ref('all');
  const searchQuery = ref('');
  const favorites = ref(JSON.parse(localStorage.getItem('sport_favorites') || '[]'));
  const loading = ref(false);
  const error = ref(null);

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

  const featuredMatches = ref([]);
  const matches = ref([]);
  const liveMatches = ref([]);

  const sportIconMap = {
    'Football': '⚽', 'Basketball': '🏀', 'Volleyball': '🏐',
    'Tennis': '🎾', 'Formula 1': '🏎️', 'Combat Sports': '🥊',
    'Table Tennis': '🏓', 'Netball': '🤾', 'Handball': '🤾‍♂️',
    'American Football': '🏈', 'Baseball': '⚾', 'Horse Racing': '🏇',
    'WM 2026': '🏆', 'Cricket': '🏏', 'Ice Hockey': '🏒',
  };

  const countryFlagMap = {
    'England': '🏴󠁧󠁢󠁥󠁮󠁧󠁿', 'Germany': '🇩🇪', 'Spain': '🇪🇸', 'Italy': '🇮🇹',
    'France': '🇫🇷', 'Turkey': '🇹🇷', 'USA': '🇺🇸', 'Brazil': '🇧🇷',
    'Australia': '🇦🇺', 'International': '🌍', 'Portugal': '🇵🇹',
    'Netherlands': '🇳🇱', 'Belgium': '🇧🇪', 'Argentina': '🇦🇷',
    'Mexico': '🇲🇽', 'Japan': '🇯🇵', 'South Korea': '🇰🇷',
  };

  async function fetchSports() {
    try {
      const { data } = await axios.get(`${API_BASE}/sports`);
      if (data.data && data.data.length > 0) {
        sports.value = data.data.map(s => ({
          id: s.slug,
          dbId: s.id,
          name: s.name,
          icon: sportIconMap[s.name] || '🏅',
          count: s.matches_count || 0,
        }));
      }
    } catch (e) {
      console.warn('Using fallback sports data');
    }
  }

  async function fetchFeaturedMatches() {
    try {
      const { data } = await axios.get(`${API_BASE}/matches/featured`);
      if (data.data) {
        featuredMatches.value = data.data.map(mapMatchFromApi);
      }
    } catch (e) {
      console.warn('Using fallback featured matches');
      featuredMatches.value = getFallbackFeatured();
    }
  }

  async function fetchMatches(params = {}) {
    loading.value = true;
    try {
      const queryParams = {};
      if (selectedSport.value && selectedSport.value !== 'all') {
        const sport = sports.value.find(s => s.id === selectedSport.value);
        if (sport && sport.dbId) queryParams.sport = sport.dbId;
      }
      if (selectedTimeFilter.value === 'today') queryParams.date = 'today';
      else if (selectedTimeFilter.value === '24h') queryParams.date = '24h';
      else if (selectedTimeFilter.value === '48h') queryParams.date = '48h';

      const { data } = await axios.get(`${API_BASE}/matches`, { params: queryParams });
      if (data.data) {
        matches.value = data.data.map(mapMatchFromApi);
      }
    } catch (e) {
      console.warn('Using fallback matches');
      matches.value = getFallbackMatches();
    } finally {
      loading.value = false;
    }
  }

  async function fetchLiveMatches() {
    try {
      const { data } = await axios.get(`${API_BASE}/matches/live`);
      if (data.data) {
        liveMatches.value = data.data.map(mapMatchFromApi);
      }
    } catch (e) {
      console.warn('No live matches available');
    }
  }

  function mapMatchFromApi(m) {
    const league = m.league || {};
    const sport = league.sport || {};
    const country = league.country || {};
    const markets = m.markets || [];
    const h2hMarket = markets.find(mk => mk.name === '1X2' || mk.name === 'Match Winner') || markets[0];
    const odds = { home: null, draw: null, away: null };

    if (h2hMarket && h2hMarket.odds) {
      h2hMarket.odds.forEach(o => {
        if (o.label === '1') odds.home = parseFloat(o.value);
        else if (o.label === 'X') odds.draw = parseFloat(o.value);
        else if (o.label === '2') odds.away = parseFloat(o.value);
      });
    }

    const startTime = new Date(m.start_time);
    return {
      id: m.id,
      homeTeam: m.home_team,
      awayTeam: m.away_team,
      league: league.name || 'Unknown',
      country: country.name || 'International',
      countryFlag: countryFlagMap[country.name] || '🌍',
      date: startTime.toISOString().split('T')[0],
      time: startTime.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }),
      odds,
      isLive: m.status === 'live',
      minute: m.status === 'live' ? Math.floor((Date.now() - startTime.getTime()) / 60000) : null,
      score: (m.score_home !== null && m.score_away !== null) ? { home: m.score_home, away: m.score_away } : null,
      sport: sport.slug || 'football',
      markets,
    };
  }

  function getFallbackFeatured() {
    return [
      { id: 'f1', homeTeam: 'Real Madrid', awayTeam: 'FC Barcelona', homeFlag: '🇪🇸', awayFlag: '🇪🇸', league: 'La Liga', country: 'Spain', date: '2026-07-02', time: '21:00', odds: { home: 2.10, draw: 3.40, away: 3.25 }, isLive: false, minute: null, score: null },
      { id: 'f2', homeTeam: 'Manchester City', awayTeam: 'Liverpool FC', homeFlag: '🇬🇧', awayFlag: '🇬🇧', league: 'Premier League', country: 'England', date: '2026-07-02', time: '17:30', odds: { home: 1.95, draw: 3.60, away: 3.50 }, isLive: true, minute: 67, score: { home: 2, away: 1 } },
      { id: 'f3', homeTeam: 'Bayern Munich', awayTeam: 'Borussia Dortmund', homeFlag: '🇩🇪', awayFlag: '🇩🇪', league: 'Bundesliga', country: 'Germany', date: '2026-07-03', time: '18:30', odds: { home: 1.65, draw: 4.00, away: 4.75 }, isLive: false, minute: null, score: null },
      { id: 'f4', homeTeam: 'Paris Saint-Germain', awayTeam: 'Olympique Marseille', homeFlag: '🇫🇷', awayFlag: '🇫🇷', league: 'Ligue 1', country: 'France', date: '2026-07-03', time: '21:00', odds: { home: 1.45, draw: 4.50, away: 6.00 }, isLive: false, minute: null, score: null },
      { id: 'f5', homeTeam: 'Galatasaray', awayTeam: 'Fenerbahce', homeFlag: '🇹🇷', awayFlag: '🇹🇷', league: 'Super Lig', country: 'Turkey', date: '2026-07-02', time: '20:00', odds: { home: 2.00, draw: 3.40, away: 3.50 }, isLive: false, minute: null, score: null },
    ];
  }

  function getFallbackMatches() {
    return [
      { id: 'm1', homeTeam: 'Arsenal', awayTeam: 'Chelsea', league: 'Premier League', country: 'England', countryFlag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿', date: '2026-07-02', time: '15:00', odds: { home: 1.80, draw: 3.70, away: 4.20 }, isLive: false, sport: 'football' },
      { id: 'm2', homeTeam: 'Tottenham', awayTeam: 'Manchester United', league: 'Premier League', country: 'England', countryFlag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿', date: '2026-07-02', time: '17:30', odds: { home: 2.40, draw: 3.30, away: 2.80 }, isLive: false, sport: 'football' },
      { id: 'm3', homeTeam: 'Atletico Madrid', awayTeam: 'Sevilla', league: 'La Liga', country: 'Spain', countryFlag: '🇪🇸', date: '2026-07-02', time: '19:00', odds: { home: 1.55, draw: 4.00, away: 5.50 }, isLive: false, sport: 'football' },
      { id: 'm4', homeTeam: 'RB Leipzig', awayTeam: 'Bayer Leverkusen', league: 'Bundesliga', country: 'Germany', countryFlag: '🇩🇪', date: '2026-07-02', time: '18:30', odds: { home: 2.60, draw: 3.40, away: 2.55 }, isLive: true, minute: 34, score: { home: 1, away: 0 }, sport: 'football' },
      { id: 'm5', homeTeam: 'Inter Milan', awayTeam: 'Napoli', league: 'Serie A', country: 'Italy', countryFlag: '🇮🇹', date: '2026-07-03', time: '20:45', odds: { home: 1.85, draw: 3.60, away: 3.90 }, isLive: false, sport: 'football' },
      { id: 'm6', homeTeam: 'Galatasaray', awayTeam: 'Fenerbahce', league: 'Super Lig', country: 'Turkey', countryFlag: '🇹🇷', date: '2026-07-02', time: '20:00', odds: { home: 2.00, draw: 3.40, away: 3.50 }, isLive: false, sport: 'football' },
      { id: 'm7', homeTeam: 'LA Lakers', awayTeam: 'Boston Celtics', league: 'NBA', country: 'USA', countryFlag: '🇺🇸', date: '2026-07-02', time: '02:00', odds: { home: 1.75, draw: null, away: 2.10 }, isLive: false, sport: 'basketball' },
    ];
  }

  const filteredMatches = computed(() => {
    let result = matches.value;

    if (selectedSport.value && selectedSport.value !== 'all') {
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
    fetchMatches();
  }

  function setTimeFilter(filter) {
    selectedTimeFilter.value = filter;
    fetchMatches();
  }

  async function init() {
    await Promise.all([
      fetchSports(),
      fetchFeaturedMatches(),
      fetchMatches(),
    ]);
  }

  return {
    selectedSport,
    selectedTimeFilter,
    searchQuery,
    favorites,
    sports,
    featuredMatches,
    matches,
    liveMatches,
    loading,
    error,
    filteredMatches,
    matchesByCountry,
    toggleFavorite,
    isFavorite,
    selectSport,
    setTimeFilter,
    fetchSports,
    fetchFeaturedMatches,
    fetchMatches,
    fetchLiveMatches,
    init,
  };
});
