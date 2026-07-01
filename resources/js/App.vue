<template>
  <div class="min-h-screen bg-dark-800 flex flex-col">
    <Navbar @toggle-sidebar="sidebarOpen = !sidebarOpen" />

    <!-- Mobile sidebar overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black/60 z-40 lg:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Mobile sidebar -->
    <aside
      :class="[
        'fixed top-0 left-0 h-full w-72 bg-dark-700 z-50 transform transition-transform duration-300 lg:hidden',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <div class="flex items-center justify-between p-4 border-b border-dark-400/30">
        <span class="text-lg font-bold text-white">{{ $t('nav.menu') }}</span>
        <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <Sidebar />
    </aside>

    <main class="flex-1">
      <router-view />
    </main>

    <BetSlip />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Navbar from './components/layout/Navbar.vue';
import Sidebar from './components/layout/Sidebar.vue';
import BetSlip from './components/sports/BetSlip.vue';

const sidebarOpen = ref(false);
</script>
