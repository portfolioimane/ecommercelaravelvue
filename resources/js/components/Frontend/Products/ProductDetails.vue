<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4 text-dark">Product Details</h2>
    <div v-if="product" class="card shadow-lg border-0 rounded-lg">
      <div class="row g-0">
        <div class="col-md-6">
          <img :src="`/storage/${product.image}`" class="card-img-top rounded-start" alt="Product Image" />
        </div>
        <div class="col-md-6">
          <div class="card-body p-4">
            <h3 class="card-title text-dark font-weight-bold">{{ product.name }}</h3>
            <p class="card-text text-muted" style="font-size: 1rem;">{{ product.description }}</p>
            
            <div class="mb-3">
              <p class="font-weight-bold pricegolden">Price: ${{ product.price }}</p>
            </div>

            <div class="mb-3">
              <p class="font-weight-bold">Category: {{ product.category.name }}</p>
            </div>

            <div class="mb-3">
              <p class="font-weight-bold">Stock: {{ product.stock }}</p>
            </div>

            <!-- Quantity Input -->
            <div class="mb-3">
              <label for="quantity" class="form-label font-weight-bold">Quantity</label>
              <input type="number" id="quantity" v-model="quantity" class="form-control" min="1" :max="product.stock" />
            </div>

            <button @click="addToCart" class="btn btn-golden btn-lg btn-block rounded-pill mt-4">Add to Cart</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      product: null,
      quantity: 1, // Default quantity
    };
  },
  created() {
    const id = this.$route.params.id;
    this.$store.dispatch('product/fetchProductById', id)
      .then(() => {
        this.product = this.$store.getters['product/allProducts'][0];
      });
  },
  methods: {
  addToCart() {
    // Ensure the quantity is valid
    if (this.quantity < 1 || this.quantity > this.product.stock) {
      alert('Please select a valid quantity');
      return;
    }

    // Check if the user is authenticated
    const isAuthenticated = this.$store.getters['auth/isAuthenticated'];

    // If the user is not authenticated (guest)
    let sessionId = null;

    if (!isAuthenticated) {
      // Check if session_id exists in sessionStorage for guest users
      sessionId = sessionStorage.getItem('session_id');

      // If session_id does not exist, generate and store it
      if (!sessionId) {
        sessionId = this.generateSessionId();
        sessionStorage.setItem('session_id', sessionId);
      }
    }

    // Now dispatch the action to add product to cart, passing the session_id (null for authenticated users)
    this.$store.dispatch('cart/addProductToCart', {
      product: this.product,
      quantity: this.quantity,
    })
      .then(() => {
        // Optionally redirect to cart page or show success message
        this.$router.push('/cart');
      })
      .catch(error => {
        console.error('Error adding product to cart:', error);
      });
  },

  // Generate a new session ID if it doesn't exist
  generateSessionId() {
    return 'sess_' + Math.random().toString(36).substr(2, 9); // Generates a random session ID
  }

  }
};
</script>

<style scoped>
.card-img-top {
  height: 350px;
  object-fit: cover;
  border-top-left-radius: 15px;
  border-bottom-left-radius: 15px;
}

.card-body {
  padding: 2rem;
}

.card-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #333;
}

.card-text {
  font-size: 1rem;
  color: #666;
}

.text-primary {
  color: #0066cc;
}



.card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 15px;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.row.g-0 {
  margin: 0;
}

.form-label {
  font-size: 1rem;
}

.form-control {
  font-size: 1rem;
  padding: 0.75rem;
}
.pricegolden{
  color:#000 !important;
  font-weight:bold;
}
</style>
