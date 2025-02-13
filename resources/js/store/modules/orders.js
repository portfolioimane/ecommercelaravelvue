import axios from '../../utils/axios.js';

// State - Manages orders
const state = {
  orders: [],
    totalOrders: 0,
};

// Mutations - Handle synchronous changes to the state
const mutations = {
  setOrders(state, { orders, total }) {
    state.orders = orders;
    state.totalOrders = total;
  },
  addOrder(state, order) {
    state.orders.push(order); // Add a new order to the orders array
  },
};

// Actions - Handle asynchronous logic (or commit mutations)
const actions = {
  async fetchOrders({ commit }) {
    try {
      const response = await axios.get('/orders');
      commit('setOrders', response.data);
    } catch (error) {
      console.error('Error fetching orders:', error);
    }
  },
 async fetchPaginatedOrders({ commit }, { page = 1, perPage = 5 }) {
    try {
      const response = await axios.get(`/myorders?page=${page}&per_page=${perPage}`);
      commit("setOrders", { orders: response.data.orders, total: response.data.total });
    } catch (error) {
      console.error("Error fetching orders:", error);
    }
  },


  // Action to submit a new order
  async submitOrder({ commit }, orderData) {
    try {
      const response = await axios.post('/orders/create', orderData);
      const order = response.data.order; // Extract the order ID from the response
      return order; // Return the ID back to the component
    } catch (error) {
      console.error('Error placing order:', error);
      throw error;
    }
  },

  // Action to fetch a single order by ID
  async fetchOrderById(_, orderId) {
    try {
      const response = await axios.get(`/orders/${orderId}`);
      return response.data; // Return the fetched order details
    } catch (error) {
      console.error('Error fetching order by ID:', error);
      throw error;
    }
  },

  // Create Stripe Payment Intent
  async createStripePayment({ commit }, totalAmount) {
    try {
      const response = await axios.post('/orders/create-stripe-payment', { total: totalAmount });
      return response.data; // Return clientSecret to be used in the component
    } catch (error) {
      console.error('Error creating Stripe payment:', error);
      throw error;
    }
  },

  // Confirm PayPal Payment
  async confirmPayPalPayment(_, paypalOrderId) {
    try {
      const response = await axios.post('/orders/confirm-paypal-payment', { paypalOrderId });
      return response.data; // Return the response (e.g., payment status)
      console.log('confirmpaypal',response.data.status);
    } catch (error) {
      console.error('Error confirming PayPal payment:', error);
      throw error;
    }
  },
};

// Getters - Derived state based on the store's state
const getters = {
  allOrders: (state) => state.orders,
    orderCount: (state) => state.totalOrders,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
