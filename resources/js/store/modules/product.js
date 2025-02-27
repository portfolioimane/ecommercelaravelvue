import axios from '../../utils/axios.js';

const state = {
  products: [],
  filteredProducts: [], // New state to hold filtered products
  categories: [],
  productVariants: [],
  featuredProducts: [], // New state property to hold featured products
  wishlist: [], // Wishlist management
  searchQuery: '',
  selectedCategory: '',
  priceRange: { min: 0, max: 10000 }, // Default price range
  productDetails: null,  // New state for product details
};

const mutations = {
  setProducts(state, products) {
    state.products = products;
    state.filteredProducts = products; // Initially, all products are displayed
  },
  setCategories(state, categories) {
    state.categories = categories;
  },
    setProductDetails(state, product) {
  state.productDetails = product; // Directly set productDetails
},

  setProductVariants(state, variants) {
    state.productVariants = variants;
  },
    setFeaturedProducts(state, featuredProducts) { // Mutation to set featured products
    state.featuredProducts = featuredProducts;
  },
  setWishlist(state, wishlist) {
    state.wishlist = wishlist;
  },
  setSearchQuery(state, query) {
    state.searchQuery = query;
    state.filteredProducts = state.products.filter(product =>
      product.name.toLowerCase().includes(query.toLowerCase())
    );
  },
  setSelectedCategory(state, category) {
    state.selectedCategory = category;
    state.filteredProducts = category
      ? state.products.filter(product => product.category.name === category)
      : state.products;
  },
  setPriceRange(state, range) {
    state.priceRange = range;
    state.filteredProducts = state.products.filter(
      product => product.price >= range.min && product.price <= range.max
    );
  },
};

const actions = {
  async fetchProducts({ commit }) {
    try {
      const response = await axios.get('/store/products');
      commit('setProducts', response.data);
    } catch (error) {
      console.error('Error fetching products:', error);
    }
  },
    async fetchFeaturedProducts({ commit }) {
    try {
      const response = await axios.get('/store/products/featured'); // Assuming the API endpoint for featured products
      commit('setFeaturedProducts', response.data);
    } catch (error) {
      console.error('Error fetching featured products:', error);
    }
  },
  async fetchCategories({ commit }) {
    try {
      const response = await axios.get('/store/categories');
      commit('setCategories', response.data);
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  },
  async fetchWishlist({ commit }) {
    try {
      const response = await axios.get('/store/wishlist');
      commit('setWishlist', response.data);
    } catch (error) {
      console.error('Error fetching wishlist:', error);
    }
  },
    async fetchProductById({ commit }, productId) {
    try {
      const response = await axios.get(`/store/products/${productId}`);
      commit('setProductDetails', response.data);
      console.log('productdetails',response.data);
    } catch (error) {
      console.error('Error fetching product details:', error);
    }
  },
    async fetchProductVariants({ commit }, productId) {  // New action to fetch variants
    try {
      const response = await axios.get(`/store/products/${productId}/variants`);
      commit('setProductVariants', response.data);
      console.log('product variants', response.data);
      } catch (error) {
      console.error('Error fetching product variants:', error);
    }
  },
};

const getters = {
  allProducts: state => state.filteredProducts,
  featuredProducts: (state) => state.featuredProducts, 
  allCategories: state => state.categories,
  wishlist: state => state.wishlist,
  productDetails: state => state.productDetails,
  allProductVariants: (state) => state.productVariants,  // New getter for variants

};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
