<template>
  <div class="container mt-5">
    <h2 class="section-title">Featured Products</h2>
    <div class="row">
      <div v-for="product in products" :key="product.id" class="col-lg-3 col-md-6 mb-4">
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
</template>

<script>
import { reactive } from 'vue';

export default {
  data() {
    return {
      wishlistMessages: reactive({}),  // Use reactive for wishlistMessages
    };
  },
  computed: {
    products() {
      return this.$store.getters['product/featuredProducts'];  // Fetch featured products from the store
    },
    isAuthenticated() {
      return this.$store.getters['auth/isAuthenticated'];  // Check if the user is authenticated
    }
  },
  methods: {
    viewDetails(productId) {
      this.$router.push(`/product/${productId}`);
    },
    async toggleWishlist(productId) {
      try {
        if (this.isInWishlist(productId)) {
          // If the product is already in the wishlist, remove it
          await this.$store.dispatch('wishlist/removeFromWishlist', productId);
          this.wishlistMessages[productId] = 'Product removed from wishlist!';
        } else {
          // If the product is not in the wishlist, add it
          await this.$store.dispatch('wishlist/addToWishlist', productId);
          this.wishlistMessages[productId] = 'Product added to wishlist!';
        }

        // Hide the message after 3 seconds
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
      return this.$store.getters['wishlist/isInWishlist'](productId);  // Check if the product is in the wishlist
    }
  },
created() {
    this.$store.dispatch('product/fetchFeaturedProducts');  // Fetch featured products from the store

    // Fetch wishlist only if authenticated
    if (this.isAuthenticated) {
      this.$store.dispatch('wishlist/fetchWishlist');  // Fetch wishlist when authenticated
    }
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

.text-primary {
  color: #0066cc;
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

.btn-outline-dark {
  border-color: #333;
  color: #333;
  transition: all 0.3s ease;
}

.btn-outline-dark:hover {
  background-color: #333;
  color: #fff;
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
  color: #e74c3c; /* Color when added to wishlist */
}

.wishlist-btn:hover {
  color: #e74c3c; /* Hover effect */
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
  color: #28a745; /* Success color */
  margin-top: 1rem;
}

.wishlist-message.error {
  color: #e74c3c; /* Error color */
}

@media (max-width: 767px) {
  .card-body {
    padding: 0.75rem;
  }

  .card-title {
    font-size: 1rem;
  }

  .text-golden {
    font-size: 1rem;
  }
}
</style>
