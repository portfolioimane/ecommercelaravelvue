import axios from '../../utils/axios.js';

// State - Manages the wishlist array
const state = {
  wishlist: []
};

// Getters - Derived state based on the store's state
const getters = {
  allWishlistItems: (state) => state.wishlist,
  
  // Check if a product is in the wishlist
  isInWishlist: (state) => (productId) => {
    return state.wishlist.some(item => item.product_id === productId);
  },

    wishlistItemCount: (state) => state.wishlist.length,
};

// Actions - Handle asynchronous logic (or commit mutations)
const actions = {
  // Fetch the wishlist from the server
  async fetchWishlist({ commit }) {
    try {
      const response = await axios.get('/wishlist');
      commit('setWishlist', response.data);
    } catch (error) {
      console.error('Error fetching wishlist:', error);
    }
  },

  // Add a product to the wishlist
  async addToWishlist({ commit, dispatch }, productId) {
    try {
      await axios.post('/wishlist', { product_id: productId });
      dispatch('fetchWishlist'); // Refresh the wishlist after adding an item
    } catch (error) {
      console.error('Error adding product to wishlist:', error);
    }
  },

  // Remove a product from the wishlist
  async removeFromWishlist({ commit, dispatch }, productId) {
    try {
      await axios.delete(`/wishlist/${productId}`);
      dispatch('fetchWishlist'); // Refresh the wishlist after removing an item
    } catch (error) {
      console.error('Error removing product from wishlist:', error);
    }
  }
};

// Mutations - Handle synchronous changes to the state
const mutations = {
  // Set the wishlist array in state
  setWishlist: (state, wishlist) => {
    state.wishlist = wishlist;
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
