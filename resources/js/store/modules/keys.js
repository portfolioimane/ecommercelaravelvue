import axios from '../../utils/axios.js';

const state = {
  stripePublicKey: null,
  paypalPublicKey: null,
};

const mutations = {
  SET_STRIPE_PUBLIC_KEY(state, key) {
    state.stripePublicKey = key;
  },
  SET_PAYPAL_PUBLIC_KEY(state, key) {
    state.paypalPublicKey = key;
  },
};

const actions = {
  // Fetch Stripe public key
  async fetchStripePublicKey({ commit }) {
    try {
      const response = await axios.get('/stripe/public-key');
      commit('SET_STRIPE_PUBLIC_KEY', response.data.publicKey);
    } catch (error) {
      console.error('Error fetching Stripe public key:', error);
    }
  },

  // Fetch PayPal public key
  async fetchPaypalPublicKey({ commit }) {
    try {
      const response = await axios.get('/paypal/public-key');
      commit('SET_PAYPAL_PUBLIC_KEY', response.data.publicKey);
    } catch (error) {
      console.error('Error fetching PayPal public key:', error);
    }
  },
};

const getters = {
  getStripePublicKey: (state) => state.stripePublicKey,
  getPaypalPublicKey: (state) => state.paypalPublicKey,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
