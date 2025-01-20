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
  async updateProductVariant({ commit }, { id, productVariantData }) {
    try {
      const response = await axios.put(`/admin/productvariant/${id}`, productVariantData);
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
  }
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
