<template>
  <div class="max-w-[1440px] mx-auto px-4 sm:px-6 py-6">
    <!-- Page header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-white mb-2">{{ pageTitle }}</h1>
      <p class="text-sm text-gray-500">{{ pageSubtitle }}</p>
    </div>

    <!-- Category tabs -->
    <div class="flex gap-2 mb-6 overflow-x-auto hide-scrollbar pb-1">
      <button
        v-for="cat in categories"
        :key="cat.id"
        @click="selectedCategory = cat.id"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-medium whitespace-nowrap transition-all',
          selectedCategory === cat.id
            ? 'bg-primary-600 text-white'
            : 'bg-dark-500 text-gray-400 hover:bg-dark-400 hover:text-gray-300'
        ]"
      >
        <span class="mr-1.5">{{ cat.icon }}</span>
        {{ cat.name }}
      </button>
    </div>

    <!-- Search bar -->
    <div class="mb-6">
      <div class="relative max-w-md">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search games..."
          class="input-field pl-10"
        />
      </div>
    </div>

    <!-- Games grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
      <div
        v-for="game in filteredGames"
        :key="game.id"
        class="group relative bg-dark-500 rounded-xl border border-dark-300/30 overflow-hidden hover:border-primary-500/30 transition-all duration-300"
      >
        <!-- Game thumbnail placeholder -->
        <div class="aspect-[3/4] relative overflow-hidden">
          <div
            :class="[
              'w-full h-full flex items-center justify-center text-4xl',
              game.bgClass
            ]"
          >
            {{ game.icon }}
          </div>

          <!-- Hover overlay -->
          <div class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition-colors">
              {{ $t('casino.playNow') }}
            </button>
            <button class="text-xs text-gray-400 hover:text-white transition-colors">
              {{ $t('casino.demo') }}
            </button>
          </div>

          <!-- Badge -->
          <div v-if="game.isNew" class="absolute top-2 left-2 bg-primary-500 text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase">
            {{ $t('casino.new') }}
          </div>
          <div v-if="game.isPopular" class="absolute top-2 left-2 bg-accent-gold text-dark-800 text-[9px] font-bold px-2 py-0.5 rounded uppercase">
            HOT
          </div>
        </div>

        <!-- Game info -->
        <div class="p-2.5">
          <h3 class="text-xs font-medium text-white truncate">{{ game.name }}</h3>
          <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ game.provider }}</p>
        </div>
      </div>
    </div>

    <!-- Load more -->
    <div class="flex justify-center mt-8">
      <button class="btn-secondary">
        Load More Games
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';

const route = useRoute();
const { t } = useI18n();

const selectedCategory = ref('all');
const searchQuery = ref('');

const pageTitle = computed(() => {
  if (route.meta.category === 'slots') return t('casino.slots');
  if (route.meta.category === 'live-casino') return t('casino.liveCasino');
  return t('casino.title');
});

const pageSubtitle = computed(() => {
  return 'Explore our collection of premium casino games';
});

const categories = [
  { id: 'all', name: 'All Games', icon: '🎰' },
  { id: 'slots', name: 'Slots', icon: '🎲' },
  { id: 'table', name: 'Table Games', icon: '🃏' },
  { id: 'live', name: 'Live Casino', icon: '🎥' },
  { id: 'jackpot', name: 'Jackpots', icon: '💰' },
  { id: 'new', name: 'New', icon: '✨' },
];

const games = ref([
  { id: 1, name: 'Book of Dead', provider: 'Play\'n GO', category: 'slots', icon: '📖', bgClass: 'bg-gradient-to-br from-amber-900 to-yellow-800', isNew: false, isPopular: true },
  { id: 2, name: 'Starburst', provider: 'NetEnt', category: 'slots', icon: '⭐', bgClass: 'bg-gradient-to-br from-purple-900 to-indigo-800', isNew: false, isPopular: true },
  { id: 3, name: 'Sweet Bonanza', provider: 'Pragmatic Play', category: 'slots', icon: '🍬', bgClass: 'bg-gradient-to-br from-pink-900 to-rose-800', isNew: false, isPopular: false },
  { id: 4, name: 'Gates of Olympus', provider: 'Pragmatic Play', category: 'slots', icon: '⚡', bgClass: 'bg-gradient-to-br from-blue-900 to-sky-800', isNew: true, isPopular: false },
  { id: 5, name: 'Blackjack VIP', provider: 'Evolution', category: 'table', icon: '🂡', bgClass: 'bg-gradient-to-br from-emerald-900 to-green-800', isNew: false, isPopular: false },
  { id: 6, name: 'European Roulette', provider: 'Evolution', category: 'table', icon: '🎡', bgClass: 'bg-gradient-to-br from-red-900 to-rose-800', isNew: false, isPopular: true },
  { id: 7, name: 'Crazy Time', provider: 'Evolution', category: 'live', icon: '🎪', bgClass: 'bg-gradient-to-br from-yellow-900 to-orange-800', isNew: false, isPopular: true },
  { id: 8, name: 'Lightning Roulette', provider: 'Evolution', category: 'live', icon: '⚡', bgClass: 'bg-gradient-to-br from-violet-900 to-purple-800', isNew: true, isPopular: false },
  { id: 9, name: 'Mega Moolah', provider: 'Microgaming', category: 'jackpot', icon: '🦁', bgClass: 'bg-gradient-to-br from-amber-900 to-orange-800', isNew: false, isPopular: false },
  { id: 10, name: 'Wolf Gold', provider: 'Pragmatic Play', category: 'slots', icon: '🐺', bgClass: 'bg-gradient-to-br from-slate-900 to-gray-800', isNew: false, isPopular: false },
  { id: 11, name: 'Baccarat', provider: 'Evolution', category: 'table', icon: '🎴', bgClass: 'bg-gradient-to-br from-teal-900 to-cyan-800', isNew: false, isPopular: false },
  { id: 12, name: 'Deal or No Deal', provider: 'Evolution', category: 'live', icon: '💼', bgClass: 'bg-gradient-to-br from-orange-900 to-red-800', isNew: true, isPopular: false },
  { id: 13, name: 'Gonzo\'s Quest', provider: 'NetEnt', category: 'slots', icon: '🗿', bgClass: 'bg-gradient-to-br from-emerald-900 to-teal-800', isNew: false, isPopular: false },
  { id: 14, name: 'Reactoonz', provider: 'Play\'n GO', category: 'slots', icon: '👾', bgClass: 'bg-gradient-to-br from-fuchsia-900 to-pink-800', isNew: false, isPopular: false },
  { id: 15, name: 'Immortal Romance', provider: 'Microgaming', category: 'slots', icon: '🧛', bgClass: 'bg-gradient-to-br from-red-950 to-rose-900', isNew: false, isPopular: false },
  { id: 16, name: 'Monopoly Live', provider: 'Evolution', category: 'live', icon: '🎩', bgClass: 'bg-gradient-to-br from-green-900 to-emerald-800', isNew: false, isPopular: true },
  { id: 17, name: 'Texas Hold\'em', provider: 'Evolution', category: 'table', icon: '🃏', bgClass: 'bg-gradient-to-br from-blue-950 to-indigo-900', isNew: false, isPopular: false },
  { id: 18, name: 'Fire Joker', provider: 'Play\'n GO', category: 'slots', icon: '🃏', bgClass: 'bg-gradient-to-br from-orange-950 to-amber-900', isNew: true, isPopular: false },
]);

const filteredGames = computed(() => {
  let result = games.value;

  if (selectedCategory.value !== 'all') {
    if (selectedCategory.value === 'new') {
      result = result.filter(g => g.isNew);
    } else {
      result = result.filter(g => g.category === selectedCategory.value);
    }
  }

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter(g =>
      g.name.toLowerCase().includes(q) ||
      g.provider.toLowerCase().includes(q)
    );
  }

  return result;
});
</script>
