import { createStore } from 'vuex'; // Updated import
import auth from './modules/auth.js';
import cart from './modules/cart.js';
import orders from './modules/orders.js';
import product from './modules/product.js';
import category from './modules/category.js';
import keys from './modules/keys.js';
import backendProducts from './modules/backend/products.js';
import backendCategories from './modules/backend/categories.js';
import backendOrders from './modules/backend/orders.js';
import paymentSetting from './modules/backend/paymentSetting.js';
import backendHomePageHeader from './modules/backend/HomePageHeader.js';
import backendVariant from './modules/backend/variant.js';
import backendVariantCombinations from './modules/backend/variantCombinations.js';
import backendProductVariant from './modules/backend/productVariant.js';
import backendReview from './modules/backend/review.js';
import backendUsers from './modules/backend/users.js';
import backendGeneralCustomize from './modules/backend/generalCustomize.js';
import generalCustomize from './modules/generalcustomize.js';
import reviews from './modules/review.js';
import wishlist from './modules/wishlist.js';
import contact from './modules/contact.js';
import backendContact from './modules/backend/contact.js';

const store = createStore({ // Use createStore for Vue 3
  modules: {
    auth,
    cart,
    orders,
    product,
    category,
    backendProducts,
    backendCategories,
    backendOrders,
    paymentSetting,
    keys,
    backendHomePageHeader,
    backendVariant,
    backendVariantCombinations,
    backendProductVariant,
    backendReview,
    backendUsers,
    reviews,
    wishlist,
    backendGeneralCustomize,
    generalCustomize,
    contact,
    backendContact


  },
});

export default store;

