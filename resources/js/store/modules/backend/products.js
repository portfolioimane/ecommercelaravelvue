import axios from '../../../utils/axios.js';

const state = {
  products: [],
  product: {
    id: null,
    name: '',
    description: '',
    price: 0.0,
    stock: 0,
    category_id: null,
    image: null,
  },
};

const mutations = {
  SET_PRODUCTS(state, products) {
    state.products = products;
  },
  SET_PRODUCT(state, product) {
    state.product = { ...product }; // Spread the product object to ensure a fresh copy
  },
  UPDATE_PRODUCT(state, updatedProduct) {
    const index = state.products.findIndex(product => product.id === updatedProduct.id);
    if (index !== -1) {
      state.products.splice(index, 1, { ...updatedProduct });
    }
  },
};

const actions = {
  async fetchProducts({ commit }) {
    try {
      const response = await axios.get('/admin/products');
      commit('SET_PRODUCTS', response.data);
    } catch (error) {
      console.error('Error fetching products:', error);
    }
  },
  async fetchProductById({ commit }, productId) {
    try {
      const response = await axios.get(`/admin/products/${productId}`);
      commit('SET_PRODUCT', response.data);
    } catch (error) {
      console.error('Error fetching product:', error);
    }
  },
 async addProduct({ commit }, formData) {
    try {


     const response = await axios.post('/admin/products', formData, {
          headers: {
            'Content-Type': 'multipart/form-data', // Ensure the header supports FormData
          },
        });

      dispatch('fetchProducts');
    } catch (error) {
      console.error('Error adding product:', error);
    }
  },
  async updateProduct({ dispatch }, { id, formData }) {
    try {

      await axios.post(`/admin/products/${id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        params: {
          _method: 'PUT', // Correct usage of PUT for update
        },
      });

      dispatch('fetchProducts');
    } catch (error) {
      console.error('Error updating product:', error);
    }
  },
  async deleteProduct({ dispatch }, productId) {
    try {
      await axios.delete(`/admin/products/${productId}`);
      dispatch('fetchProducts');
    } catch (error) {
      console.error('Error deleting product:', error);
    }
  },
};

const getters = {
  allProducts: (state) => state.products,
  currentProduct: (state) => state.product,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
