<template>
  <div class="h-full overflow-y-auto py-3 px-2">
    <!-- Search -->
    <div class="px-2 mb-3">
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input
          v-model="sportsStore.searchQuery"
          type="text"
          :placeholder="$t('nav.search')"
          class="w-full bg-dark-500 border border-dark-300/40 rounded-lg pl-9 pr-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-primary-500/50"
        />
      </div>
    </div>

    <!-- Events By Date dropdown -->
    <div class="px-2 mb-3">
      <button
        @click="showDatePicker = !showDatePicker"
        class="w-full flex items-center justify-between px-3 py-2.5 bg-dark-500 rounded-lg border border-dark-300/30 text-sm text-gray-300 hover:border-dark-200/50 transition-colors"
      >
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <span>{{ $t('sports.eventsByDate') }}</span>
        </div>
        <svg
          :class="['w-4 h-4 transition-transform duration-200', showDatePicker ? 'rotate-180' : '']"
          fill="none" stroke="currentColor" viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div v-if="showDatePicker" class="mt-1 bg-dark-500 rounded-lg border border-dark-300/30 p-2">
        <input
          type="date"
          class="w-full bg-dark-600 border border-dark-300/40 rounded px-3 py-1.5 text-xs text-white focus:outline-none focus:border-primary-500/50"
        />
      </div>
    </div>

    <!-- Favorites section -->
    <div v-if="favoriteSports.length > 0" class="mb-2">
      <div class="px-3 py-1.5 text-[10px] font-semibold text-gray-500 uppercase tracking-widest">
        {{ $t('sports.favorites') }}
      </div>
      <div
        v-for="sport in favoriteSports"
        :key="sport.id"
        @click="sportsStore.selectSport(sport.id)"
        :class="['sport-item', { active: sportsStore.selectedSport === sport.id }]"
      >
        <span class="sport-icon">{{ sport.icon }}</span>
        <span class="flex-1 truncate">{{ $t(`sports.${sport.id}`) }}</span>
        <button
          @click.stop="sportsStore.toggleFavorite(sport.id)"
          class="text-accent-gold hover:scale-110 transition-transform"
        >
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
        </button>
      </div>
      <div class="border-b border-dark-400/20 my-2 mx-3"></div>
    </div>

    <!-- All Sports -->
    <div class="px-3 py-1.5 text-[10px] font-semibold text-gray-500 uppercase tracking-widest">
      {{ $t('sports.allSports') }}
    </div>

    <div
      v-for="sport in sportsStore.sports"
      :key="sport.id"
      @click="sportsStore.selectSport(sport.id)"
      :class="['sport-item group', { active: sportsStore.selectedSport === sport.id }]"
    >
      <span class="sport-icon">{{ sport.icon }}</span>
      <span class="flex-1 truncate">{{ $t(`sports.${sport.id}`) }}</span>
      <button
        @click.stop="sportsStore.toggleFavorite(sport.id)"
        :class="[
          'opacity-0 group-hover:opacity-100 transition-opacity',
          sportsStore.isFavorite(sport.id) ? 'text-accent-gold' : 'text-gray-600 hover:text-gray-400'
        ]"
      >
        <svg class="w-3.5 h-3.5" :fill="sportsStore.isFavorite(sport.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
      </button>
      <span class="sport-count">{{ sport.count }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useSportsStore } from '../../store/sports';

const sportsStore = useSportsStore();
const showDatePicker = ref(false);

const favoriteSports = computed(() => {
  return sportsStore.sports.filter(s => sportsStore.isFavorite(s.id));
});
</script>
