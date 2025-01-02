import { createStore } from 'vuex'; // Updated import
import auth from './modules/auth.js';
import cart from './modules/cart.js';
import orders from './modules/orders.js';
import product from './modules/product.js';
import category from './modules/category.js';
import backendProducts from './modules/backend/products.js';
import backendCategories from './modules/backend/categories.js';
import paymentSetting from './modules/backend/paymentSetting.js';


const store = createStore({ // Use createStore for Vue 3
  modules: {
    auth,
    cart,
    orders,
    product,
    category,
    backendProducts,
    backendCategories,
    paymentSetting,
  },
});

export default store;

