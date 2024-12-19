import axios from '../../utils/axios.js';

// State - Manages orders and their associated items
const state = {
  orders: [],
  orderItems: [],
};

// Mutations - Handle synchronous changes to the state
const mutations = {
  setOrders(state, orders) {
    state.orders = orders;
  },
  setOrderItems(state, orderItems) {
    state.orderItems = orderItems;
  },
};

// Actions - Handle asynchronous logic (or commit mutations)
const actions = {
  async fetchOrders({ commit }) {
    try {
      const response = await axios.get('/orders');
      commit('setOrders', response.data);

      // Assuming the response contains order items as well
      const orderItemsResponse = await axios.get('/order-items');
      commit('setOrderItems', orderItemsResponse.data);
    } catch (error) {
      console.error('Error fetching orders or order items:', error);
    }
  },
};

// Getters - Derived state based on the store's state
const getters = {
  allOrders: (state) => state.orders,
  allOrderItems: (state) => state.orderItems,
  orderCount: (state) => state.orders.length,
  orderItemsCount: (state) => state.orderItems.length,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
