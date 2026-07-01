<template>
  <div class="max-w-[1440px] mx-auto">
    <!-- Mobile sport selector -->
    <div class="lg:hidden px-3 py-3 border-b border-dark-400/20">
      <div class="flex gap-2 overflow-x-auto hide-scrollbar snap-x pb-1">
        <button
          v-for="sport in sportsStore.sports"
          :key="sport.id"
          @click="sportsStore.selectSport(sport.id)"
          :class="['mobile-sport-pill', { active: sportsStore.selectedSport === sport.id }]"
        >
          <span class="pill-icon">{{ sport.icon }}</span>
          <span>{{ sport.name }}</span>
        </button>
      </div>
    </div>

    <div class="flex">
      <!-- Left Sidebar (desktop) -->
      <aside class="hidden lg:block w-60 xl:w-64 flex-shrink-0 bg-dark-700 border-r border-dark-400/20 min-h-[calc(100vh-56px)] sticky top-14 h-[calc(100vh-56px)] overflow-y-auto">
        <Sidebar />
      </aside>

      <!-- Main content -->
      <div class="flex-1 min-w-0 px-3 sm:px-4 lg:px-6 py-4">
        <!-- Featured Matches -->
        <FeaturedMatches />

        <!-- Section header -->
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            {{ $t('match.upcoming') }}
          </h2>
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500">
              {{ sportsStore.filteredMatches.length }} {{ $t('match.allMatches').toLowerCase() }}
            </span>
          </div>
        </div>

        <!-- Match List -->
        <MatchList />
      </div>

      <!-- Right Sidebar (desktop) -->
      <aside class="hidden xl:block w-72 flex-shrink-0 p-4 sticky top-14 h-[calc(100vh-56px)] overflow-y-auto">
        <RightSidebar />
      </aside>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useSportsStore } from '../../store/sports';
import { useAuthStore } from '../../store/auth';
import Sidebar from '../../components/layout/Sidebar.vue';
import RightSidebar from '../../components/layout/RightSidebar.vue';
import FeaturedMatches from '../../components/sports/FeaturedMatches.vue';
import MatchList from '../../components/sports/MatchList.vue';

const sportsStore = useSportsStore();
const authStore = useAuthStore();

onMounted(async () => {
  await sportsStore.init();
  if (authStore.token) {
    await authStore.fetchUser();
  }
});
</script>
