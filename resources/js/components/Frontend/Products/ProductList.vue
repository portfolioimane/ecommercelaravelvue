<template>
  <div class="container mt-5">
    <div class="row">
      <!-- Sidebar with Filters -->
      <div class="col-lg-3">
        <div class="filter-sidebar">
          <h5 class="filter-title">Filters</h5>

          <!-- Search Bar -->
          <div class="filter-item">
            <label for="search" class="filter-label">Search</label>
            <input 
              type="text" 
              v-model="searchQuery" 
              id="search" 
              class="form-control" 
              placeholder="Search products..." 
            />
          </div>

          <!-- Price Range Filter -->
          <div class="filter-item">
            <label for="priceRange" class="filter-label">Price Range</label>
            <input type="range" v-model="priceRange" min="0" max="1000" step="10" class="form-control" id="priceRange" />
            <div class="d-flex justify-content-between">
              <span>{{ minPrice }} MAD</span>
              <span>{{ priceRange }}+ MAD</span>
            </div>
          </div>

          <!-- Category Filter (List) -->
          <div class="filter-item">
            <h6 class="filter-label">Categories</h6>
            <ul class="category-list">
              <li 
                @click="selectedCategory = ''" 
                :class="{'selected': selectedCategory === ''}">
                All
              </li>
              <li 
                v-for="category in allCategories" 
                :key="category.id" 
                @click="selectedCategory = category.id" 
                :class="{'selected': selectedCategory === category.id}">
                {{ category.name }}
              </li>
            </ul>
          </div>

        </div>
      </div>

      <!-- Products Grid -->
      <div class="col-lg-9">
        <h2 class="section-title">All Products</h2>
        <div class="row">
          <div v-for="product in filteredProducts" :key="product.id" class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-lg h-100 position-relative">
              <img :src="`/${product.image}`" class="card-img-top rounded-top" alt="Product" />
              <div class="card-body p-3">
                <h5 class="card-title text-dark font-weight-bold">{{ product.name }}</h5>
                <p class="card-text text-muted" style="font-size: 0.875rem;">{{ product.description }}</p>
                <p class="text-muted" style="font-size: 0.8rem;">Category: <span class="font-weight-bold">{{ product.category.name }}</span></p>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <p class="font-weight-bold text-golden" style="font-size: 1rem;">${{ product.price }}</p>
                  <p class="font-weight-bold text-success" style="font-size: 0.9rem;">Stock: {{ product.stock }}</p>
                </div>
                <button @click="viewDetails(product.id)" class="btn btn-golden mt-3">View Details</button>
              </div>

              <!-- Wishlist Icon Button -->
              <button 
                v-if="isAuthenticated"
                @click="toggleWishlist(product.id)"
                class="wishlist-btn"
                :class="{'added': isInWishlist(product.id)}">
                <i class="fas fa-heart"></i>
              </button>
              <p v-else class="text-center text-muted mt-2">Log in to add to your wishlist.</p>

              <!-- Wishlist Status Message -->
              <p v-if="wishlistMessages[product.id]" class="wishlist-message text-center mt-2">{{ wishlistMessages[product.id] }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>



<script>
import { reactive } from 'vue';
import { mapGetters, mapActions } from 'vuex';

export default {
  data() {
    return {
      priceRange: 0, // Price filter range
      selectedCategory: '', // Selected category filter
      searchQuery: '', // User's search input
      wishlistMessages: reactive({}),
    };
  },
  computed: {
    ...mapGetters('product', ['allProducts']),
    ...mapGetters('category', ['allCategories']),
    
    filteredProducts() {
      return this.allProducts.filter(product => {
        // Filter products by price, category, and search query
        const isPriceValid = product.price >= this.priceRange;
        const isCategoryValid = this.selectedCategory ? product.category.id === this.selectedCategory : true;
        const isSearchValid = product.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || product.description.toLowerCase().includes(this.searchQuery.toLowerCase());

        return isPriceValid && isCategoryValid && isSearchValid;
      });
    },
    
    isAuthenticated() {
      return this.$store.getters['auth/isAuthenticated'];
    }
  },
  methods: {
    ...mapActions('wishlist', ['addToWishlist', 'removeFromWishlist']),
    
    viewDetails(productId) {
      this.$router.push(`/product/${productId}`);
    },
    async toggleWishlist(productId) {
      try {
        if (this.isInWishlist(productId)) {
          await this.removeFromWishlist(productId);
          this.wishlistMessages[productId] = 'Product removed from wishlist!';
        } else {
          await this.addToWishlist(productId);
          this.wishlistMessages[productId] = 'Product added to wishlist!';
        }

        setTimeout(() => {
          this.wishlistMessages[productId] = null;
        }, 3000);
      } catch (error) {
        this.wishlistMessages[productId] = 'Error updating wishlist!';
        setTimeout(() => {
          this.wishlistMessages[productId] = null;
        }, 3000);
        console.error('Error updating wishlist:', error);
      }
    },
    isInWishlist(productId) {
      return this.$store.getters['wishlist/isInWishlist'](productId);
    }
  },
  created() {
    this.$store.dispatch('product/fetchProducts');
    this.$store.dispatch('category/fetchCategories');

    if (this.isAuthenticated) {
      this.$store.dispatch('wishlist/fetchWishlist');
    };
  }
};
</script>


<style scoped>
.section-title {
  font-size: 2rem;
  font-weight: bold;
  text-align: center;
  margin-bottom: 2.5rem;
  color: #333;
}

.filter-sidebar {
  padding: 15px;
  background-color: #f7f7f7;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.filter-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: #333;
}

.filter-item {
  margin-bottom: 1.5rem;
}

.filter-label {
  display: block;
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  width: 100%;
  height: 8px;
  background: #D4AF37; /* Golden color */
  border-radius: 5px;
  outline: none;
  transition: background 0.3s ease;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  background: #D4AF37; /* Golden thumb */
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.3s ease;
}

input[type="range"]::-moz-range-thumb {
  width: 20px;
  height: 20px;
  background: #D4AF37; /* Golden thumb */
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.3s ease;
}

input[type="range"]:focus {
  background: #D4AF37; /* Slightly lighter gold when focused */
}

input[type="range"]::-webkit-slider-thumb:hover {
  background: #D4AF37;
}

input[type="range"]::-moz-range-thumb:hover {
  background:#D4AF37;
}

.card-img-top {
  height: 200px;
  object-fit: cover;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}

.card-body {
  padding: 1rem;
}

.card-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #333;
}

.card-text {
  font-size: 0.875rem;
  color: #666;
}

.text-muted {
  font-size: 0.8rem;
}

.text-success {
  color: #28a745;
}

.card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 10px;
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.text-golden {
  color: #ffd700 !important;
  font-weight: bold;
}

.wishlist-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  color: #ccc;
  font-size: 1.5rem;
  transition: color 0.3s ease;
  cursor: pointer;
  z-index: 10;
}

.wishlist-btn.added {
  color: #e74c3c;
}

.wishlist-btn:hover {
  color: #e74c3c;
}

.text-center.text-muted {
  font-size: 0.9rem;
  color: #999;
  margin-top: 1rem;
  font-style: italic;
}

.wishlist-message {
  font-size: 0.9rem;
  font-style: italic;
  color: #28a745;
  margin-top: 1rem;
}

.wishlist-message.error {
  color: #e74c3c;
}

.category-list {
  list-style-type: none;
  padding: 0;
}

.category-list li {
  padding: 10px;
  cursor: pointer;
  color: #555;
  font-size: 1rem;
}

.category-list li:hover {
  background-color: #D4AF37;
  color: #fff;
}

.category-list li.selected {
  background-color: #D4AF37;
  color: #fff;
}

@media (max-width: 767px) {
  .filter-sidebar {
    position: relative;
    top: 0;
    margin-bottom: 2rem;
  }

  .filter-title {
    font-size: 1.2rem;
  }
}

.filter-item {
  margin-bottom: 1.5rem;
}

#search {
  width: 100%;
  padding: 8px;
  font-size: 1rem;
  border-radius: 5px;
  border: 1px solid #ccc;
  margin-bottom: 15px;
  transition: border-color 0.3s ease;
}

#search:focus {
  border-color: #D4AF37; /* Golden color */
  outline: none;
}
</style>




