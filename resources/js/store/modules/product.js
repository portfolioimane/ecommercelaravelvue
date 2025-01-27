import axios from '../../utils/axios.js';

// State - Manages the products array
const state = {
  products: [],
  productVariants: [],  // Add a new state property to manage variants
};

// Mutations - Handle synchronous changes to the state
const mutations = {
  setProducts(state, products) {
    state.products = products;
  },
  setProductDetails(state, product) {
    state.products = [product];
  },
  setProductVariants(state, variants) {  // Add mutation to set variants
    state.productVariants = variants;
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
  async fetchProductVariants({ commit }, productId) {  // New action to fetch variants
    try {
      const response = await axios.get(`/store/products/${productId}/variants`);
      commit('setProductVariants', response.data);
      console.log('product variants', response.data);
      } catch (error) {
      console.error('Error fetching product variants:', error);
    }
  },
};

// Getters - Derived state based on the store's state
const getters = {
  allProducts: (state) => state.products,
  productCount: (state) => state.products.length,
  allProductVariants: (state) => state.productVariants,  // New getter for variants
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
