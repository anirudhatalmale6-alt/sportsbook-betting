<template>
  <button
    :class="['odds-btn', { active }]"
    @click="$emit('click')"
  >
    <span class="odds-label">{{ label }}</span>
    <span class="odds-value">{{ formattedOdds }}</span>
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  label: {
    type: String,
    required: true,
  },
  odds: {
    type: Number,
    default: null,
  },
  active: {
    type: Boolean,
    default: false,
  },
  format: {
    type: String,
    default: 'decimal', // decimal, fractional, american
  },
});

defineEmits(['click']);

const formattedOdds = computed(() => {
  if (props.odds == null || isNaN(props.odds)) return '-';
  const oddsFormat = localStorage.getItem('odds_format') || 'decimal';

  if (oddsFormat === 'fractional') {
    return decimalToFractional(props.odds);
  }
  if (oddsFormat === 'american') {
    return decimalToAmerican(props.odds);
  }
  return props.odds.toFixed(2);
});

function decimalToFractional(decimal) {
  const frac = decimal - 1;
  // Simple fraction approximation
  const denominator = 100;
  const numerator = Math.round(frac * denominator);
  const gcd = getGCD(numerator, denominator);
  return `${numerator / gcd}/${denominator / gcd}`;
}

function decimalToAmerican(decimal) {
  if (decimal >= 2) {
    return '+' + Math.round((decimal - 1) * 100);
  }
  return '-' + Math.round(100 / (decimal - 1));
}

function getGCD(a, b) {
  a = Math.abs(a);
  b = Math.abs(b);
  while (b) {
    [a, b] = [b, a % b];
  }
  return a;
}
</script>
