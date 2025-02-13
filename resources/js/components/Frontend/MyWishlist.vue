<template>
  <div class="container mt-5">
    <h2 class="section-title">My Wishlist</h2>

    <div v-if="loading" class="loading-message text-center">Loading wishlist...</div>

    <div v-else>
      <div v-if="wishlist.length === 0" class="empty-message text-center">
        Your wishlist is empty.
      </div>

      <div v-else class="row">
        <div v-for="item in wishlist" :key="item.product.id" class="col-lg-3 col-md-6 mb-4">
          <div class="card shadow-sm border-0 rounded-lg h-100 position-relative">
            <img :src="`/${item.product.image}`" class="card-img-top rounded-top" alt="Product" />
            <div class="card-body p-3">
              <h5 class="card-title text-dark font-weight-bold">{{ item.product.name }}</h5>
              <p class="card-text text-muted" style="font-size: 0.875rem;">{{ item.product.description }}</p>
           
              <div class="d-flex justify-content-between align-items-center mt-2">
                <p class="font-weight-bold text-golden" style="font-size: 1rem;">{{ item.product.price }} MAD</p>
                <p class="font-weight-bold text-success" style="font-size: 0.9rem;">Stock: {{ item.product.stock }}</p>
              </div>

              <button @click="viewDetails(item.product.id)" class="btn btn-golden btn-block rounded-pill mt-3">
                View Details
              </button>
            </div>

            <!-- Remove from Wishlist Button -->
            <button 
              @click="removeFromWishlist(item.product.id)" 
              class="wishlist-btn added">
            <i class="fas fa-heart"></i>
            </button>

          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";

export default {
  name: "MyWishlist",
  data() {
    return {
      loading: true,
    };
  },
  computed: {
    ...mapState("wishlist", ["wishlist"]),
  },
  methods: {
    ...mapActions("wishlist", ["fetchWishlist", "removeFromWishlist"]),
    viewDetails(productId) {
      this.$router.push(`/product/${productId}`); // Navigate to product details
    },
  },
  async mounted() {
    await this.fetchWishlist();
    this.loading = false;
  },
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
  color: #e74c3c;
  font-size: 1.5rem;
  transition: color 0.3s ease;
  cursor: pointer;
  z-index: 10;
}

.wishlist-btn:hover {
  color: #c0392b;
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
