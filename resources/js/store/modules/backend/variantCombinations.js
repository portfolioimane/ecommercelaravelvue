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
async updateAllCombinations({ commit }, formData) {
  try {
    const response = await axios.post('/admin/variant-combinations/update', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    // Commit data to Vuex state directly without status check
    commit('SET_COMBINATIONS', response.data);
    return response.data;

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