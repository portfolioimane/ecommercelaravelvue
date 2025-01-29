import { createRouter, createWebHistory } from 'vue-router';
import store from '../store/index.js';

// Import the layout
import AppLayout from '../components/Frontend/Layout/AppLayout.vue';
import AdminLayout from '../components/Admin/Layout/AdminLayout.vue'; // Admin Layout

// Import route components
import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import FeaturedProduct from '../components/Frontend/Products/FeaturedProduct.vue';
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
import Home from '../components/Frontend/Home.vue';
import Review from '../components/Frontend/Products/Review.vue';
import ProductList from '../components/Frontend/Products/ProductList.vue';

import MyWishlist from '../components/Frontend/MyWishlist.vue';

// Admin Components
import AdminDashboard from '../components/Admin/Dashboard/AdminDashboard.vue';
import AddProduct from '../components/Admin/Products/AddProduct.vue';
import EditProduct from '../components/Admin/Products/EditProduct.vue';
import Products from '../components/Admin/Products/Products.vue';
import CreateProductVariant from '../components/Admin/Products/CreateProductVariant.vue';
import ProductVariant from '../components/Admin/Products/ProductVariant.vue';
import ProductVariantDetails from '../components/Admin/Products/ProductVariantDetails.vue';


import Variant from '../components/Admin/Variant/Variant.vue';
import CreateVariant from '../components/Admin/Variant/CreateVariant.vue';
import CreateVariantValue from '../components/Admin/Variant/CreateVariantValue.vue';


import PaymentSetting from '../components/Admin/Settings/PaymentSetting.vue';

import GeneralCustomize from '../components/Admin/Customize/GeneralCustomize.vue';


import Categories from '../components/Admin/Categories/Categories.vue';
import Orders from '../components/Admin/Orders/Orders.vue';
import BackendOrderDetails from '../components/Admin/Orders/OrderDetails.vue';
import HomePageHeader from '../components/Admin/Customize/HomePageHeader.vue';

import AddReview from '../components/Admin/Review/AddReview.vue';
import ReviewList from '../components/Admin/Review/ReviewList.vue';
import Customers from '../components/Admin/Customers/Customers.vue';







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
        name: 'Home',
        path: '/',
        component: Home,
      },
      {
        name: 'ProductDetails',
        path: '/product/:id',
        component: ProductDetails,
      },
           {
        name: 'ProductList',
        path: '/shop',
        component: ProductList,
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
              {
        name: 'MyWishlist',
        path: 'wishlist',
        component: MyWishlist,
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
        path: 'createproductvariant/:id',
        name: 'CreateProductVariant',
        component: CreateProductVariant,
      },
           {
        path: 'productvariant',
        name: 'ProductVariant',
        component: ProductVariant,
      },
                {
        path: 'productvariant/:id',
        name: 'ProductVariantDetails',
        component: ProductVariantDetails,
      },
  
      {
        path: 'products/edit/:id',
        name: 'EditProduct',
        component: EditProduct,
      },
            {
        name: 'Variant',
        path: 'variant',
        component: Variant,
      },
      {
        name: 'CreateVariant',
        path: 'createvariant',
        component: CreateVariant,
      },
     {
        name: 'CreateVariantValue',
        path: 'variant/:id/createvariantvalue',
        component: CreateVariantValue,
      },
         {
        name: 'Categories',
        path: 'categories',
        component: Categories,
      },
            {
        name: 'Orders',
        path: 'orders',
        component: Orders,
      },
               {
        name: 'Customers',
        path: 'customers',
        component: Customers,
      },
       {
        name: 'AddReview',
        path: 'reviews/add',
        component: AddReview,
      },
       {
        name: 'Reviews',
        path: 'reviews',
        component: ReviewList,
      },

      {
            name: 'BackendOrderDetails',
            path: 'order/:id',
            component: BackendOrderDetails,
       },
       {
        name: 'PaymentSetting',
        path: 'paymentsetting',
        component: PaymentSetting,
      },
              {
        name: 'GeneralCustomize',
        path: 'generalcustomize',
        component: GeneralCustomize,
      },
      {
        name: 'HomePageHeader',
        path: 'customize/homepageheader',
        component: HomePageHeader,
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
