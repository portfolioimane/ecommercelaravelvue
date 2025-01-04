import axios from '../../utils/axios.js';

// State
const state = {
  categories: [],
};

// Mutations
const mutations = {
  setCategories(state, categories) {
    state.categories = categories;
  },
};

// Actions
const actions = {
  async fetchCategories({ commit }) {
    try {
      const response = await axios.get('/store/categories');
      commit('setCategories', response.data);
      console.log("categories", response.data);
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  },
};

// Getters
const getters = {
  allCategories: (state) => state.categories,
  categoryCount: (state) => state.categories.length,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
