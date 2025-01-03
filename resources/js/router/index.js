import { createRouter, createWebHistory } from 'vue-router';
import store from '../store/index.js';

// Import the layout
import AppLayout from '../components/Frontend/Layout/AppLayout.vue';
import AdminLayout from '../components/Admin/Layout/AdminLayout.vue'; // Admin Layout

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

// Admin Components
import AdminDashboard from '../components/Admin/Dashboard/AdminDashboard.vue';
import AddProduct from '../components/Admin/Products/AddProduct.vue';
import EditProduct from '../components/Admin/Products/EditProduct.vue';
import Products from '../components/Admin/Products/Products.vue';
import PaymentSetting from '../components/Admin/Settings/PaymentSetting.vue';


import Categories from '../components/Admin/Categories/Categories.vue';


const routes = [
  {
    path: '/',
    component: AppLayout,
    children: [
      {
        name: 'Login',
        path: '/login',
        component: Login,
      },
      {
        name: 'Register',
        path: '/register',
        component: Register,
      },
      {
        name: 'ResetPassword',
        path: '/password/reset/:token',
        component: ResetPassword,
        props: true,
      },
      {
        name: 'ForgotPassword',
        path: '/forgotpassword',
        component: ForgotPassword,
        props: true,
      },
      {
        name: 'ProductList',
        path: '/',
        component: ProductList,
      },
      {
        name: 'ProductDetails',
        path: '/product/:id',
        component: ProductDetails,
      },
      {
        name: 'Cart',
        path: '/cart',
        component: Cart,
      },
      {
        name: 'Checkout',
        path: '/checkout',
        component: Checkout,
        meta: { requiresAuth: true },
      },
      {
        name: 'OrderHistory',
        path: '/orders',
        component: OrderHistory,
        meta: { requiresAuth: true },
      },
      {
        path: '/customerdashboard',
        component: CustomerLayout,
        meta: { requiresAuth: true },
        children: [
          {
            name: 'Profile',
            path: 'profile',
            component: Profile,
          },
          {
            name: 'MyOrders',
            path: 'myorders',
            component: MyOrders,
          },
          {
            name: 'OrderDetails',
            path: 'order/:id',
            component: OrderDetails,
            meta: { requiresAuth: true },
          },
        ],
      },
    ],
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAdmin: true },
    children: [
      {
        name: 'AdminDashboard',
        path: 'dashboard',
        component: AdminDashboard,
      },
      {
        name: 'Products',
        path: 'products',
        component: Products,
      },
      {
        name: 'AddProduct',
        path: 'products/add',
        component: AddProduct,
      },
      {
        path: 'products/edit/:id',
        name: 'EditProduct',
        component: EditProduct,
      },
         {
        name: 'Categories',
        path: 'categories',
        component: Categories,
      },
       {
        name: 'PaymentSetting',
        path: 'paymentsetting',
        component: PaymentSetting,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const isAuthenticated = store.getters['auth/isAuthenticated'];
  const user = store.getters['auth/user']; // Fetch the user from Vuex store
  const authChecked = store.getters['auth/authChecked']; // Get auth check status

  if (!authChecked) {
    // Wait until the auth check is finished
    return next();
  }

  if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
    next({ path: '/login' });
  } else if (to.matched.some(record => record.meta.requiresAdmin)) {
    if (!user || user.role !== 'admin') {
      next({ path: '/' });
    } else {
      next();
    }
  } else {
    next();
  }
});



export default router;
