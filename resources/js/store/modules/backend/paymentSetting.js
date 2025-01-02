import axios from '../../../utils/axios.js'; // Import axios instance (adjust the path if needed)

const state = {
  stripeEnabled: false,
  stripePublicKey: '',
  stripeSecretKey: '',
  paypalEnabled: false,
  paypalClientId: '',
  paypalSecretKey: '',
};

const mutations = {
  SET_PAYMENT_SETTINGS(state, settings) {
    // Set both Stripe and PayPal settings (or any other providers you have)
    if (settings.provider === 'stripe') {
      state.stripeEnabled = settings.enabled;
      state.stripePublicKey = settings.public_key;
      state.stripeSecretKey = settings.secret_key;
    } else if (settings.provider === 'paypal') {
      state.paypalEnabled = settings.enabled;
      state.paypalClientId = settings.public_key;
      state.paypalSecretKey = settings.secret_key;
    }
  },
  UPDATE_PAYMENT_SETTINGS(state, settings) {
    if (settings.provider === 'stripe') {
      state.stripeEnabled = settings.enabled;
      state.stripePublicKey = settings.public_key;
      state.stripeSecretKey = settings.secret_key;
    } else if (settings.provider === 'paypal') {
      state.paypalEnabled = settings.enabled;
      state.paypalClientId = settings.public_key;
      state.paypalSecretKey = settings.secret_key;
    }
  },
};

const actions = {
  async fetchPaymentSettings({ commit }) {
    try {
      const response = await axios.get('/admin/paymentsetting'); // Ensure the correct API endpoint
      const settings = response.data; // Assuming the response is an array of payment settings
      settings.forEach(setting => {
        commit('SET_PAYMENT_SETTINGS', setting); // Commit for each payment provider (e.g., Stripe, PayPal)
      });
    } catch (error) {
      console.error('Error fetching payment settings:', error);
    }
  },

  async updatePaymentSettings({ commit }, paymentSettings) {
    try {
      const response = await axios.put('/admin/paymentsetting/update', paymentSettings);
      commit('UPDATE_PAYMENT_SETTINGS', paymentSettings); // Commit the updated settings
    } catch (error) {
      console.error('Error updating payment settings:', error);
    }
  },
};


const getters = {
  isStripeEnabled: (state) => state.stripeEnabled,
  stripePublicKey: (state) => state.stripePublicKey,
  stripeSecretKey: (state) => state.stripeSecretKey,
  isPaypalEnabled: (state) => state.paypalEnabled,
  paypalClientId: (state) => state.paypalClientId,
  paypalSecretKey: (state) => state.paypalSecretKey,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
