import { createRouter, createWebHistory } from 'vue-router';
import store from '../store/index.js';

// Import the layout
import AppLayout from '../components/Frontend/Layout/AppLayout.vue';

// Import route components
import Login from '../components/Frontend/Auth/Login.vue';
import Register from '../components/Frontend/Auth/Register.vue';
import ProductList from '../components/Frontend/Products/ProductList.vue';
import ProductDetails from '../components/Frontend/Products/ProductDetails.vue';
import Cart from '../components/Frontend/Cart/Cart.vue';
import Checkout from '../components/Frontend/Cart/Checkout.vue';
import OrderHistory from '../components/Frontend/Orders/OrderHistory.vue';


 const routes = [
  {
      path: '/',
      component: AppLayout,
      children: [
      {
      path: '/login',
      name: 'Login',
      component: Login,
      },
      {
      path: '/register',
      name: 'Register',
      component: Register,
      },
      {
      path: '/',
      name: 'ProductList',
      component:  ProductList,
      },

      {
       path: '/product/:id',
       name: 'ProductDetails',
      component: ProductDetails,
      },
      { 
        path: '/cart',
        name: 'Cart',
        component: Cart,
      },
        { 
         path: '/checkout',
          name: 'Checkout',
         component: Checkout,
       },
        { 
          path: '/orders',
          name: 'OrderHistory',
          component: OrderHistory,
         },
      ]
    }
  ];


const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard for authentication and admin role check
router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters['auth/isAuthenticated'];
  const role = localStorage.getItem('user-role');
  console.log('role', role);

  if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
    next({ path: '/login' });
  } else if (to.matched.some(record => record.meta.requiresAdmin) && role !== 'admin') {
    next({ path: '/' });
  } else {
    next();
  }
});

export default router;
