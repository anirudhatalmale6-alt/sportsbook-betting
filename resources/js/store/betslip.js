import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from './auth';

const API_BASE = '/api';

export const useBetslipStore = defineStore('betslip', () => {
  const selections = ref([]);
  const isOpen = ref(false);
  const stake = ref(10);
  const betType = ref('single');
  const placingBet = ref(false);
  const betResult = ref(null);
  const betError = ref(null);

  const selectionCount = computed(() => selections.value.length);

  const totalOdds = computed(() => {
    if (selections.value.length === 0) return 0;
    if (betType.value === 'single') {
      return selections.value[0]?.odds ?? 0;
    }
    return selections.value.reduce((acc, sel) => acc * sel.odds, 1);
  });

  const potentialWinnings = computed(() => {
    if (betType.value === 'single') {
      return selections.value.reduce((sum, sel) => sum + (stake.value * sel.odds), 0);
    }
    return stake.value * totalOdds.value;
  });

  function addSelection(selection) {
    const existing = selections.value.findIndex(
      s => s.matchId === selection.matchId && s.market === selection.market
    );

    if (existing >= 0) {
      if (selections.value[existing].outcome === selection.outcome) {
        selections.value.splice(existing, 1);
        if (selections.value.length === 0) isOpen.value = false;
        return;
      }
      selections.value[existing] = selection;
    } else {
      selections.value.push(selection);
    }

    isOpen.value = true;
    betResult.value = null;
    betError.value = null;
  }

  function removeSelection(index) {
    selections.value.splice(index, 1);
    if (selections.value.length === 0) isOpen.value = false;
  }

  function clearAll() {
    selections.value = [];
    isOpen.value = false;
    betResult.value = null;
    betError.value = null;
  }

  function toggleOpen() {
    isOpen.value = !isOpen.value;
  }

  function isSelected(matchId, market, outcome) {
    return selections.value.some(
      s => s.matchId === matchId && s.market === market && s.outcome === outcome
    );
  }

  async function placeBet() {
    const authStore = useAuthStore();

    if (!authStore.isAuthenticated) {
      betError.value = 'Please login to place a bet';
      return null;
    }

    if (selections.value.length === 0) {
      betError.value = 'No selections added';
      return null;
    }

    placingBet.value = true;
    betError.value = null;

    try {
      const items = selections.value.map(s => ({
        odd_id: s.oddId,
      }));

      const { data } = await axios.post(`${API_BASE}/bets`, {
        items,
        stake: stake.value,
        type: betType.value,
      });

      betResult.value = data.data || data;
      await authStore.refreshBalance();
      clearAll();
      return betResult.value;
    } catch (e) {
      betError.value = e.response?.data?.message || 'Failed to place bet';
      return null;
    } finally {
      placingBet.value = false;
    }
  }

  return {
    selections,
    isOpen,
    stake,
    betType,
    placingBet,
    betResult,
    betError,
    selectionCount,
    totalOdds,
    potentialWinnings,
    addSelection,
    removeSelection,
    clearAll,
    toggleOpen,
    isSelected,
    placeBet,
  };
});
