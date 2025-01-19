import axios from '../../../utils/axios.js';

const state = {
  combinations: [],
};

const mutations = {
  SET_COMBINATIONS(state, combinations) {
    state.combinations = combinations;
  },
};

const actions = {
  async updateAllCombinations({ commit }, combinations) {
    try {
      const response = await axios.post('/admin/variant-combinations/update', {
        combinations,
      });

      if (response.status === 200) {
        commit('SET_COMBINATIONS', combinations);
        return response.data;
      }
    } catch (error) {
      console.error('Error updating combinations:', error);
      throw error;
    }
  },
};

const getters = {
  combinations: (state) => state.combinations,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
