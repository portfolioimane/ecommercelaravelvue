import axios from '../../utils/axios.js';

const state = {
  cart: [], // Holds cart items
};

const mutations = {
  setCart(state, cart) {
    state.cart = cart;
  },
  addToCart(state, product) {
    const index = state.cart.findIndex((item) => item.id === product.id);
    if (index === -1) {
      state.cart.push({ ...product, quantity: 1 });
    } else {
      state.cart[index].quantity += 1;
    }
  },
  removeFromCart(state, itemId) {
    state.cart = state.cart.filter((item) => item.id !== itemId);
  },
  updateCartItem(state, { itemId, quantity }) {
    const item = state.cart.find((item) => item.id === itemId);
    if (item) {
      item.quantity = quantity;  // Update the quantity of the cart item
    }
  },
};

const actions = {
async fetchCart({ commit }) {
  try {
    // Check if the user is authenticated
    const isAuthenticated = sessionStorage.getItem('user-token') !== null;

    let response;
    if (isAuthenticated) {
      // Fetch cart for authenticated user
      response = await axios.get('/cart');
      commit('setCart', response.data.cart_items);
    } else {
      // Fetch cart for guest user
      response = await axios.get('/cartguest');
      commit('setCart', response.data.cart_items);
    }

    console.log('Fetched cart data:', response.data.cart_items);
  } catch (error) {
    console.error('Error fetching cart:', error);
  }
},

async addProductToCart({ commit }, { product, quantity, variant_id }) {
  try {
    const isAuthenticated = sessionStorage.getItem('user-token') !== null;

    if (isAuthenticated) {
      // Authenticated user: no session_id required
      const response = await axios.post('/cart/add', {
        product_id: product.id,
        quantity,
        variant_id, // Include variant_id in the request
      });
      commit('setCart', response.data.cart_items);
    } else {
      // Guest user: session_id is required
      const response = await axios.post('/cartguest/add', {
        product_id: product.id,
        quantity,
        variant_id, // Include variant_id in the request
      });
      commit('setCart', response.data.cart_items);
    }
  } catch (error) {
    console.error('Error adding product to cart:', error);
  }
},


  async removeProductFromCart({ commit }, itemId) {
    try {
  
        const response = await axios.post('/cart/remove', {
          item_id: itemId,
        });
        commit('removeFromCart', itemId);

    } catch (error) {
      console.error('Error removing product from cart:', error);
    }
  },

  async updateCartItem({ commit }, { itemId, quantity }) {
    try {

        const response = await axios.post('/cart/update', {
          item_id: itemId,
          quantity,
        });
        commit('updateCartItem', { itemId, quantity });

    } catch (error) {
      console.error('Error updating cart item:', error);
    }
  },
};

const getters = {
  cartItems: (state) => state.cart,
  cartItemCount: (state) => {
    // Return 0 if cart is empty or undefined
    return state.cart ? state.cart.reduce((total, item) => total + item.quantity, 0) : 0;
  },
totalCartValue: (state) =>
  parseFloat(
    state.cart
      .reduce((total, item) => {
        const price = item.variant?.price || item.product.price; // Use variant price if available, otherwise use product price
        return total + item.quantity * price;
      }, 0)
      .toFixed(2)
  ),

};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
