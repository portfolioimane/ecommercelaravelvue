import axios from '../../utils/axios.js';

// State - Manages the products array
const state = {
  products: [],
};

// Mutations - Handle synchronous changes to the state
const mutations = {
  setProducts(state, products) {
    state.products = products;
  },
  setProductDetails(state, product) {
    state.products = [product];
  },
};

// Actions - Handle asynchronous logic (or commit mutations)
const actions = {
  async fetchProducts({ commit }) {
    try {
      const response = await axios.get('/store/products');
      commit('setProducts', response.data);
      } catch (error) {
      console.error('Error fetching products:', error);
    }
  },
  async fetchProductById({ commit }, productId) {
    try {
      const response = await axios.get(`/store/products/${productId}`);
      commit('setProductDetails', response.data);
    } catch (error) {
      console.error('Error fetching product details:', error);
    }
  },
};

// Getters - Derived state based on the store's state
const getters = {
  allProducts: (state) => state.products,
  productCount: (state) => state.products.length,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
