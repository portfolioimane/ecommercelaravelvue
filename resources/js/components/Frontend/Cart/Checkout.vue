<template>
  <div class="container mt-5">
    <h2>Checkout</h2>

    <!-- Cart Summary -->
    <div v-if="cartItems.length">
      <h4>Order Summary</h4>
      <ul class="list-group mb-4">
        <li
          v-for="item in cartItems"
          :key="item.id"
          class="list-group-item d-flex justify-content-between align-items-center"
        >
          <span>{{ item.name }} (x{{ item.quantity }})</span>
          <span>${{ (item.quantity * item.price).toFixed(2) }}</span>
        </li>
      </ul>
      <h5 class="text-end">Total: <span class="text-primary">${{ totalCartValue }}</span></h5>

      <!-- Checkout Form -->
      <form @submit.prevent="submitOrder">
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            class="form-control"
            placeholder="Enter your name"
            required
          />
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            class="form-control"
            placeholder="Enter your email"
            required
          />
        </div>
        <div class="mb-3">
          <label for="address" class="form-label">Address</label>
          <textarea
            id="address"
            v-model="form.address"
            class="form-control"
            placeholder="Enter your address"
            rows="3"
            required
          ></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Place Order</button>
      </form>
    </div>

    <!-- Empty Cart Message -->
    <div v-else class="alert alert-info text-center mt-4">
      <p>Your cart is empty. Add items to the cart to proceed with checkout.</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        name: '',
        email: '',
        address: '',
      },
    };
  },
  computed: {
    cartItems() {
      return this.$store.getters['cart/cartItems'];
    },
    totalCartValue() {
      return this.$store.getters['cart/totalCartValue'];
    },
  },
  methods: {
    submitOrder() {
      if (this.cartItems.length === 0) {
        alert('Your cart is empty. Add items before placing an order.');
        return;
      }

      // Simulate order submission
      console.log('Order Submitted:', {
        name: this.form.name,
        email: this.form.email,
        address: this.form.address,
        items: this.cartItems,
        total: this.totalCartValue,
      });

      // Reset cart and form after submission
      this.$store.commit('cart/setCart', []);
      this.form = {
        name: '',
        email: '',
        address: '',
      };

      alert('Order placed successfully!');
      this.$router.push('/');
    },
  },
};
</script>

<style scoped>
/* Add any specific styles for Checkout.vue */
</style>
