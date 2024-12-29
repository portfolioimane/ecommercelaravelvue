import axios from '../../../utils/axios.js';

const state = {
  categories: [],
  category: {
    id: null,
    name: '',
  },
};

const mutations = {
  SET_CATEGORIES(state, categories) {
    state.categories = categories;
  },
  SET_CATEGORY(state, category) {
    state.category = { ...category }; // Spread the category object to ensure a fresh copy
  },
  REMOVE_CATEGORY(state, categoryId) {
    state.categories = state.categories.filter(category => category.id !== categoryId);
  },
};

const actions = {
  async fetchCategories({ commit }) {
    try {
      const response = await axios.get('/admin/categories');
      commit('SET_CATEGORIES', response.data);
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  },
  async addCategory({ dispatch }, category) {
    try {
      const response = await axios.post('/admin/categories', category);
      dispatch('fetchCategories');
    } catch (error) {
      console.error('Error adding category:', error);
    }
  },
  async deleteCategory({ commit }, categoryId) {
    try {
      await axios.delete(`/admin/categories/${categoryId}`);
      commit('REMOVE_CATEGORY', categoryId);
    } catch (error) {
      console.error('Error deleting category:', error);
    }
  },
};

const getters = {
  allCategories: (state) => state.categories,
  currentCategory: (state) => state.category,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
