<template>
  <div class="space-y-3">
    <!-- World Cup 2026 Banner -->
    <div class="jilbet-promo-banner bg-gradient-to-br from-primary-900 to-dark-600">
      <div class="p-5">
        <div class="text-lg font-bold text-white mb-1">World Cup 2026</div>
        <p class="text-xs text-gray-300">All the latest odds and markets for the biggest tournament in football.</p>
      </div>
    </div>

    <!-- Betting Action Banner -->
    <div class="jilbet-promo-banner bg-gradient-to-br from-dark-500 to-dark-700">
      <div class="p-5">
        <div class="text-lg font-bold text-white mb-1">All the Betting Action</div>
        <p class="text-xs text-gray-300">Pre-match and live betting on thousands of events every day.</p>
      </div>
    </div>

    <!-- Bet Builder Banner -->
    <div class="jilbet-promo-banner bg-gradient-to-br from-primary-800/60 to-dark-600">
      <div class="p-5">
        <div class="text-lg font-bold text-white mb-1">Bet Builder</div>
        <p class="text-xs text-gray-300">Combine multiple selections from the same event into one bet.</p>
      </div>
    </div>

    <!-- Slots Banner -->
    <div class="jilbet-promo-banner bg-gradient-to-br from-accent-gold/20 to-dark-600">
      <div class="p-5">
        <div class="text-lg font-bold text-white mb-1">Slots from Top Providers</div>
        <p class="text-xs text-gray-300">Play the best slots from Pragmatic Play, NetEnt, and more.</p>
      </div>
    </div>

    <!-- Welcome Bonus -->
    <div class="jilbet-promo-banner bg-gradient-to-br from-primary-900 to-dark-500">
      <div class="p-5">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-xs font-bold text-accent-gold uppercase tracking-wider">{{ $t('promo.welcome') }}</span>
        </div>
        <h3 class="text-lg font-bold text-white mb-1">100% {{ $t('nav.deposit') }}</h3>
        <p class="text-xs text-gray-400 mb-3">{{ $t('promo.welcomeDesc') }}</p>
        <button class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 rounded-lg text-sm transition-colors">
          {{ $t('promo.claimNow') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>
