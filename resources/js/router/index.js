import { createRouter, createWebHistory } from 'vue-router';
import store from '../store/index.js';

// Import the layout
import AppLayout from '../components/Frontend/Layout/AppLayout.vue';

// Import route components
import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import ProductList from '../components/Frontend/Products/ProductList.vue';
import ProductDetails from '../components/Frontend/Products/ProductDetails.vue';
import Cart from '../components/Frontend/Cart/Cart.vue';
import Checkout from '../components/Frontend/Cart/Checkout.vue';
import OrderHistory from '../components/Frontend/Orders/OrderHistory.vue';
import OrderDetails from '../components/Frontend/Orders/OrderDetails.vue';
import MyOrders from '../components/Frontend/Orders/MyOrders.vue';
import ResetPassword from '../components/Frontend/ResetPassword.vue';
import ForgotPassword from '../components/Frontend/ForgotPassword.vue';
import Profile from '../components/Frontend/Profile.vue';
import CustomerLayout from '../components/Frontend/CustomerLayout.vue';




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
  path: '/password/reset/:token', // :token will capture the token in the URL
  name: 'ResetPassword',
  component: ResetPassword, // The Vue component you created for the reset password form
  props: true, // This allows you to pass the route params to the component
},
{
  path: '/forgotpassword', // :token will capture the token in the URL
  name: 'ForgotPassword',
  component: ForgotPassword, // The Vue component you created for the reset password form
  props: true, // This allows you to pass the route params to the component
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
          meta: { requiresAuth: true },
         component: Checkout,
       },
   
        { 
          path: '/orders',
          meta: { requiresAuth: true },
          name: 'OrderHistory',
          component: OrderHistory,
         },

      {
  path: '/customerdashboard',
  component: CustomerLayout,
  meta: { requiresAuth: true },
  children: [
{
      path: 'profile',
      name: 'profile',
      component: Profile,
    }, 
    {
      path: 'myorders',
      name: 'MyOrders',
      component: MyOrders,
    }, 
       {
       path: 'order/:id',
       name: 'OrderDetails',
       meta: { requiresAuth: true },
      component: OrderDetails,
      },  
  ]
},
      ]
    },


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
