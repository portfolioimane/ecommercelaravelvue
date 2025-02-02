import axios from '../../../utils/axios.js';

const state = {
  productvariants: []
};

const mutations = {
  SET_PRODUCTVARIANTS(state, productvariants) {
    state.productvariants = productvariants;
  },
  UPDATE_PRODUCTVARIANT(state, updatedProductVariant) {
    const index = state.productvariants.findIndex(productvariant => productvariant.id === updatedProductVariant.id);
    if (index !== -1) {
      state.productvariants[index] = updatedProductVariant;
    }
  },
  REMOVE_PRODUCTVARIANT(state, productvariantId) {
    state.productvariants = state.productvariants.filter(productvariant => productvariant.id !== productvariantId);
  },
  setProductVariants(state, productvariants) {
    // Clear the product variants array and set a new one
    state.productvariants = productvariants;
  }
};

const actions = {
  async fetchProductVariants({ commit }, productId) {
    try {
      const response = await axios.get(`/admin/product/${productId}/productvariants`);
      commit('SET_PRODUCTVARIANTS', response.data);
      console.log('productvariant', response.data);
    } catch (error) {
      console.error('Error fetching product variants:', error);
    }
  },
  async updateProductVariant({ commit }, { id, formData }) {
    try {
      const response = await axios.post(`/admin/productvariantupdate/${id}`, formData);
      commit('UPDATE_PRODUCTVARIANT', response.data);
    } catch (error) {
      console.error('Error updating product variant:', error);
    }
  },
  async deleteProductVariant({ commit }, id) {
    try {
      await axios.delete(`/admin/productvariant/${id}`);
      commit('REMOVE_PRODUCTVARIANT', id);
    } catch (error) {
      console.error('Error deleting product variant:', error);
    }
  },
  async deleteAllProductVariants({ commit }, productId) {
    try {
      // Call your API or backend to delete all variants for the given product
      await axios.delete(`/admin/products/${productId}/variants`);
      
      // Clear all product variants from the Vuex state
      commit('setProductVariants', []); // Clear variants in the store
      alert('All variants have been deleted.');
    } catch (error) {
      console.error('Error deleting variants:', error);
      alert('There was an error deleting the variants.');
    }
  },
};

const getters = {
  getProductVariants: state => state.productvariants
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
};
