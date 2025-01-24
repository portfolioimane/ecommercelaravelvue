<template>
  <div class="container mt-5">
    <h2 class="mb-4 text-center text-golden">Your Cart</h2>

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
            <th scope="col">Variant</th>
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
              <img :src="`/storage/${item.product.image}`"  alt="Product Image" class="cart-item-image" />
              <div class="product-name">{{ item.product.name }}</div>
            </td>
<td>
  <div v-if="item.variant?.combination_values" class="combination-values">
    <div 
      v-for="(value, key) in item.variant.combination_values" 
      :key="key" 
      class="combination-item"
    >
      <span class="key-label">{{ key }}:</span>
      <span v-if="key === 'color'">
        <span 
          class="color-circle" 
          :style="{ backgroundColor: value }"
        ></span>
      </span>
      <span v-else>{{ value }}</span>
    </div>
  </div>
  <span v-else>N/A</span>
</td>
            <!-- Product Price -->
<td class="align-middle">
  <span v-if="item.variant?.price">
    ${{ item.variant.price }}
  </span>
  <span v-else>
    ${{ item.product.price }}
  </span>
</td>            <!-- Product Quantity -->
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
  <span v-if="item.variant?.price">
    ${{ (item.variant.price * item.quantity).toFixed(2) }}
  </span>
  <span v-else>
    ${{ (item.product.price * item.quantity).toFixed(2) }}
  </span>
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
  <h4 class="text-end">
    Total: <span class="text-golden">${{ totalCartValue }}</span>
  </h4>
  <button @click="proceedToCheckout" class="btn btn-golden btn-block rounded-pill mt-3">Proceed to Checkout</button>
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
               console.log('Fetched cart data:', this.cartItems); // Log cart items after fetching
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
    proceedToCheckout() {
    if (this.$store.getters['auth/isAuthenticated']) {
      this.$router.push({ name: 'Checkout' }); // Redirect to Checkout page
    } else {
      this.$router.push({ name: 'Login', query: { redirect: 'Checkout' } }); // Add redirect query    

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

.text-golden{
color:#000 !important;
}

.btn-outline-success{
  color:#d4af37 !important;
  border:1px solid #d4af37 !important;
}
.btn-outline-success:hover{
  color:#fff !important;
  border:1px solid #d4af37 !important;
  background-color:#d4af37;
}

.btn-outline-warning{
  color:#d4af37 !important;
  border:1px solid #d4af37 !important;
}
.btn-outline-warning:hover{
 color:#fff !important;
  border:1px solid #d4af37 !important;
  background-color:#d4af37; 
}

.spinner {
  border: 4px solid #d4af37;
  border-top: 4px solid #d4af37;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.combination-values {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.combination-item {
  display: flex;
  align-items: center;
  font-size: 0.9rem;
}

.key-label {
  font-weight: bold;
  margin-right: 5px;
}

.color-circle {
  display: inline-block;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  border: 1px solid #ccc;
  margin-left: 5px;
}

</style>
