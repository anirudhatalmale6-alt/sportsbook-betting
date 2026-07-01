import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useBetslipStore = defineStore('betslip', () => {
  const selections = ref([]);
  const isOpen = ref(false);
  const stake = ref(10);
  const betType = ref('single'); // single, accumulator

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
    // selection = { matchId, matchLabel, market, outcome, odds }
    const existing = selections.value.findIndex(
      s => s.matchId === selection.matchId && s.market === selection.market
    );

    if (existing >= 0) {
      // Same match + market: toggle or replace
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
  }

  function removeSelection(index) {
    selections.value.splice(index, 1);
    if (selections.value.length === 0) isOpen.value = false;
  }

  function clearAll() {
    selections.value = [];
    isOpen.value = false;
  }

  function toggleOpen() {
    isOpen.value = !isOpen.value;
  }

  function isSelected(matchId, market, outcome) {
    return selections.value.some(
      s => s.matchId === matchId && s.market === market && s.outcome === outcome
    );
  }

  function placeBet() {
    // Placeholder: would call API
    const bet = {
      selections: [...selections.value],
      stake: stake.value,
      type: betType.value,
      totalOdds: totalOdds.value,
      potentialWinnings: potentialWinnings.value,
      timestamp: new Date().toISOString(),
    };
    console.log('Bet placed:', bet);
    clearAll();
    return bet;
  }

  return {
    selections,
    isOpen,
    stake,
    betType,
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
