import { createStore } from 'vuex'; // Updated import
import auth from './modules/auth.js';
import cart from './modules/cart.js';
import orders from './modules/order.js';
import product from './modules/product.js';
import category from './modules/category.js';



const store = createStore({ // Use createStore for Vue 3
  modules: {
    auth,
    cart,
    orders,
    product,
    category,
  },
});

export default store;

