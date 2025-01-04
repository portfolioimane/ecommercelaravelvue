import axios from '../../../utils/axios.js';

const state = {
  orders: [],
  currentOrder: null,
};

const mutations = {
  SET_ORDERS(state, orders) {
    state.orders = orders;
  },
  SET_CURRENT_ORDER(state, order) {
    state.currentOrder = order;
  },
};

const actions = {
  // Fetch all orders
  async fetchOrders({ commit }) {
    try {
      const response = await axios.get('/admin/orders');
          console.log('Orders fetched:', response.data); // Add debug log
      commit('SET_ORDERS', response.data.orders);
    } catch (error) {
      console.error('Error fetching orders:', error);
    }
  },

  // Fetch details of a specific order
  async fetchOrderDetails({ commit }, orderId) {
    try {
      const response = await axios.get(`/admin/orders/${orderId}`);
      commit('SET_CURRENT_ORDER', response.data.order);
      console.log('orderDetails', response.data.order);
    } catch (error) {
      console.error('Error fetching order details:', error);
    }
  },

  // Update order status
  async updateOrderStatus({ commit }, { orderId, status }) {
    try {
      const response = await axios.put(`/admin/orders/${orderId}/status`, { status });
      commit('SET_CURRENT_ORDER', response.data.order); // Optionally update the current order state
      return response.data.order;
    } catch (error) {
      console.error('Error updating order status:', error);
    }
  },
};

const getters = {
  orders: (state) => state.orders,
  currentOrder: (state) => state.currentOrder,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
