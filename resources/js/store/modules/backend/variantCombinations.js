import axios from '../../../utils/axios.js';

const state = {
  combinations: [],
  existingCombinationValues: [], // Store only the existing combination values
};

const mutations = {
  SET_COMBINATIONS(state, combinations) {
    state.combinations = combinations;
  },

  SET_EXISTING_COMBINATION_VALUES(state, existingCombinationValues) {
    state.existingCombinationValues = existingCombinationValues; // Set existing combination values in state
  },
};

const actions = {
  // Action to fetch existing combination values from the backend
async fetchExistingCombinationValues({ commit }, productId) {
  try {
    const response = await axios.get(`/admin/existing-combination-values/${productId}`);
    
    // Commit only the existing combination values to Vuex state
    commit('SET_EXISTING_COMBINATION_VALUES', response.data);
  } catch (error) {
    console.error('Error fetching existing combinations:', error);
    throw error;
  }
},


  // Action to update combinations
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
  existingCombinationValues: (state) => state.existingCombinationValues, // Return only existing combination values
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
