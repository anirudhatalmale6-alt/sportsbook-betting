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
      <aside class="hidden lg:block w-56 xl:w-60 flex-shrink-0 bg-dark-700 border-r border-dark-400/20 min-h-[calc(100vh-48px)] sticky top-12 h-[calc(100vh-48px)] overflow-y-auto">
        <Sidebar />
      </aside>

      <!-- Main content -->
      <div class="flex-1 min-w-0 px-3 sm:px-4 lg:px-6 py-4">
        <!-- Featured Matches -->
        <FeaturedMatches />

        <!-- League Browser (replaces individual match rows) -->
        <MatchList />
      </div>

      <!-- Right Sidebar (desktop) -->
      <aside class="hidden xl:block w-72 flex-shrink-0 p-4 sticky top-12 h-[calc(100vh-48px)] overflow-y-auto">
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
