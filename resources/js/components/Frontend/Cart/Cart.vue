<template>
  <div class="container mt-5">
    <h2 class="mb-4 text-center text-primary">Your Cart</h2>

    <!-- Custom Loading Spinner -->
    <div v-if="loading" class="loading-container text-center">
      <div class="spinner"></div>
    </div>

    <!-- Cart Items Table -->
    <div v-else-if="cartItems && cartItems.length" class="cart-items">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th scope="col">Product</th>
            <th scope="col">Price</th>
            <th scope="col">Quantity</th>
            <th scope="col">Total</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in cartItems" :key="item.id">
            <!-- Product Image and Name -->
            <td>
              <img :src="item.product.image" alt="Product Image" class="cart-item-image" />
              <div class="product-name">{{ item.product.name }}</div>
            </td>
            <!-- Product Price -->
            <td class="align-middle">${{ item.product.price }}</td>
            <!-- Product Quantity -->
            <td class="align-middle">
              <div class="quantity-container">
                <button
                  @click="updateQuantity(item.id, item.quantity + 1)"
                  class="btn btn-outline-success btn-sm"
                >
                  +
                </button>
                <span class="quantity-text">{{ item.quantity }}</span>
                <button
                  @click="updateQuantity(item.id, item.quantity - 1)"
                  class="btn btn-outline-warning btn-sm"
                  :disabled="item.quantity <= 1"
                >
                  -
                </button>
              </div>
            </td>
            <!-- Product Total -->
<td class="align-middle">
  ${{ (item.product.price * item.quantity).toFixed(2) }}
</td>
            <!-- Remove Button -->
            <td class="align-middle">
              <button
                @click="removeFromCart(item.id)"
                class="btn btn-outline-danger btn-sm"
              >
                <i class="fas fa-trash"></i> Remove
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Total Cart Value -->
      <div class="total d-flex justify-content-between align-items-center mt-4">
        <h4 class="text-end">Total: <span class="text-primary">${{ totalCartValue }}</span></h4>
        <button class="btn btn-gold btn-lg">Proceed to Checkout</button>
      </div>
    </div>

    <!-- Empty Cart Message -->
    <div v-else class="alert alert-info text-center mt-4">
      <p>Your cart is empty.</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loading: true,  // Initialize loading state to true
    };
  },
  mounted() {
    this.fetchCart();
  },
  computed: {
    cartItems() {
      return this.$store.getters['cart/cartItems'] || [];
    },
    totalCartValue() {
      return this.$store.getters['cart/totalCartValue'];
    },
  },

  methods: {
    async fetchCart() {
      try {
        await this.$store.dispatch('cart/fetchCart');
      } catch (error) {
        console.error('Error fetching cart data:', error);
      } finally {
        this.loading = false;  // Set loading to false after fetching
      }
    },
    removeFromCart(itemId) {
      this.$store.dispatch('cart/removeProductFromCart', itemId);
    },
    async updateQuantity(itemId, newQuantity) {
      if (newQuantity > 0) {
        try {
          await this.$store.dispatch('cart/updateCartItem', { itemId, quantity: newQuantity });
        } catch (error) {
          console.error('Error updating quantity:', error);
        }
      }
    },
  },
};
</script>

<style scoped>
.cart-items {
  margin-top: 20px;
}

.table {
  border-collapse: separate;
  border-spacing: 0 10px;
}

.cart-item-image {
  max-width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 5px;
}

.product-name {
  font-size: 1.1rem;
  font-weight: bold;
}

.quantity-container {
  display: flex;
  align-items: center;
}

.quantity-text {
  margin: 0 15px;
  font-size: 1rem;
}

.btn {
  width: auto;
  height: 35px;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 15px;
  font-size: 1rem;
}

.btn:hover {
  transform: scale(1.05);
}

.total {
  margin-top: 30px;
  padding: 20px;
  border-radius: 8px;
  background-color: #f8f9fa;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.total .btn {
  padding: 0.7rem 1.5rem;
  font-size: 1.2rem;
}

.alert-info {
  font-size: 1.2rem;
  color: #17a2b8;
}

.fas {
  margin-right: 5px;
}

.btn-gold {
  background-color: #ffd700 !important;
  color: #000 !important;
  border: none !important;
}

.btn-gold:hover {
  background-color: #e6c100 !important;
  color: #000 !important;
  transform: scale(1.05);
}

@media (max-width: 768px) {
  .table td, .table th {
    padding: 10px;
  }

  .cart-item-image {
    max-width: 50px;
    height: 50px;
  }

  .quantity-container {
    margin-top: 10px;
  }
}

/* Custom Loader */
.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 200px;
}

.spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #007bff;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
