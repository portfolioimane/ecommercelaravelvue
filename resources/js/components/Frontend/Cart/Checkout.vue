<template>
  <div class="container mt-5 checkout-container">
    <h2 class="text-center mb-4">Checkout</h2>

    <div class="row">
      <!-- Customer Information -->
      <div class="col-md-6">
        <h4 class="mb-4">Customer Information</h4>
        <form @submit.prevent="submitOrder">
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              class="form-control"
              :placeholder="user ? user.name : 'Enter your name'"
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
              :placeholder="user ? user.email : 'Enter your email'"
              required
            />
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input
              type="text"
              id="phone"
              v-model="form.phone"
              class="form-control"
              :placeholder="user ? user.phone : 'Enter your phone number'"
              required
            />
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input
              type="text"
              id="address"
              v-model="form.address"
              class="form-control"
              :placeholder="user ? user.address : 'Enter your address'"
              required
            />
          </div>

          <!-- Payment Methods -->
          <h4 class="mb-4">Select Payment Method</h4>
          <div class="mb-3">
            <div class="form-check">
              <input
                type="radio"
                id="cod"
                v-model="form.payment_method"
                value="cash_on_delivery"
                class="form-check-input"
                required
              />
              <label class="form-check-label" for="cod">Cash on Delivery</label>
            </div>
            <div class="form-check">
              <input
                type="radio"
                id="stripe"
                v-model="form.payment_method"
                value="stripe"
                class="form-check-input"
              />
              <label class="form-check-label" for="stripe">Stripe</label>
            </div>
            <div class="form-check">
              <input
                type="radio"
                id="paypal"
                v-model="form.payment_method"
                value="paypal"
                class="form-check-input"
              />
              <label class="form-check-label" for="paypal">PayPal</label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100">Place Order</button>
        </form>
      </div>

      <!-- Order Summary -->
      <div class="col-md-6">
        <h4 class="mb-4 text-center">Order Summary</h4>
        <ul class="list-group">
          <li
            v-for="item in cartItems"
            :key="item.id"
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <span>{{ item.product.name }} (x{{ item.quantity }})</span>
            <span>{{ item.product.price }}</span>
            <span class="text-golden">${{ (item.quantity * item.product.price).toFixed(2) }}</span>
          </li>
        </ul>
        <h5 class="text-end mt-4">
          Total: <span class="text-golden">${{ totalCartValue.toFixed(2) }}</span>
        </h5>
      </div>
    </div>

    <!-- Empty Cart Message -->
    <div v-if="cartItems.length === 0" class="alert alert-info text-center mt-4">
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
        phone: '',
        address: '',
        payment_method: 'cash_on_delivery', // Default payment method
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
    user() {
      return this.$store.getters['auth/user']; // Get logged-in user data
    },
  },
  methods: {
    // Method to submit the order
async submitOrder() {
  if (this.cartItems.length === 0) {
    alert('Your cart is empty. Add items before placing an order.');
    return;
  }

  const orderData = {
    name: this.form.name || this.user.name,
    email: this.form.email || this.user.email,
    phone: this.form.phone || this.user.phone,
    address: this.form.address || this.user.address,
    payment_method: this.form.payment_method,
    items: this.cartItems,
    total: this.totalCartValue,
  };

  try {
    // Send the order data to the server
    const response = await this.$store.dispatch('orders/submitOrder', orderData);

    // Assuming the server returns the created order's ID
    const orderId = response.id;

    // Clear the cart and reset the form
    this.$store.commit('cart/setCart', []);
    this.form = {
      name: '',
      email: '',
      phone: '',
      address: '',
      payment_method: 'cash_on_delivery',
    };


    // Redirect to the order details page
    this.$router.push(`/customerdashboard/order/${orderId}`);
  } catch (error) {
    console.error('Error submitting order:', error);
    alert('Failed to place the order. Please try again.');
  }
},


    // Fetch cart data
    fetchCart() {
      console.log('Fetching cart data...');
      this.$store.dispatch('cart/fetchCart');
    },

    // Fetch user data and fill form
    fillUserData() {
      if (this.user) {
        this.form.name = this.user.name || '';
        this.form.email = this.user.email || '';
        this.form.phone = this.user.phone || '';
        this.form.address = this.user.address || '';
      }
    },
  },
  mounted() {
    this.fetchCart();
    this.$store.dispatch('auth/checkAuth').then(() => {
      this.fillUserData();
    });
  },
};
</script>

<style scoped>
.checkout-container {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2, h4 {
  color: #333;
}

.text-golden {
  color: #d4af37; /* Golden color */
}

.btn-primary {
  background-color: #d4af37 !important;
  border: none !important;
  color: #fff !important;
}

.btn-primary:hover {
  background-color: #b6932f !important;
}

.list-group-item {
  border: none;
  background-color: #fff;
  padding: 12px 20px;
  font-size: 1rem;
}

.list-group-item + .list-group-item {
  margin-top: 8px;
}

.list-group-item .text-golden {
  font-weight: bold;
}

.form-check-label {
  font-weight: normal;
  font-size: 1rem;
}

.form-check-input:checked {
  background-color: #E6C200 !important;
  border-color: #E6C200 !important;
}
</style>
