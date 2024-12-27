<template>
  <div v-if="order" class="container">
    <div class="order-header">
      <h1 class="order-title">Order Details</h1>
      <p class="order-id"><strong>Order ID:</strong> {{ order.id }}</p>
    </div>

    <div class="order-summary">
      <div class="order-summary-item">
        <strong>Name:</strong> {{ order.name }}
      </div>
      <div class="order-summary-item">
        <strong>Email:</strong> {{ order.email }}
      </div>
      <div class="order-summary-item">
        <strong>Phone:</strong> {{ order.phone }}
      </div>
      <div class="order-summary-item">
        <strong>Shipping Address:</strong> {{ order.address }}
      </div>
      <div class="order-summary-item">
        <strong>Payment Method:</strong> {{ order.payment_method }}
      </div>
      <div class="order-summary-item total">
        <strong>Total:</strong> <span class="total-amount">${{ order.total }}</span>
      </div>
    </div>

    <div class="order-items">
      <h2 class="order-items-title">Items Ordered</h2>
      <table class="order-items-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in order.order_items" :key="item.id">
            <td>{{ item.product.name }}</td>
            <td>${{ item.product.price }}</td>
            <td>{{ item.quantity }}</td>
            <td>${{ (item.product.price * item.quantity).toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="order-actions">
      <button @click="goBack" class="back-button">Back to Orders</button>
    </div>
  </div>

  <div v-else class="loading">
    <div class="spinner"></div> <!-- Loader Spinner -->
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import { useRouter } from 'vue-router';

export default {
  name: 'OrderDetails',
  data() {
    return {
      order: null, // Holds the fetched order details
    };
  },
  computed: {
    orderId() {
      return this.$route.params.id; // Extract the order ID from the route params
    },
  },
  methods: {
    ...mapActions('orders', ['fetchOrderById']), // Map the action from the Vuex module
    async loadOrderDetails() {
      try {
        const orderDetails = await this.fetchOrderById(this.orderId);
        this.order = orderDetails; // Assign the fetched order details
      } catch (error) {
        console.error('Error loading order details:', error);
      }
    },
    goBack() {
      this.$router.push({ name: 'MyOrders' }); // Redirect to the orders list page
    },
  },
  created() {
    this.loadOrderDetails(); // Fetch the order details when the component is created
  },
};
</script>

<style scoped>
/* Global styles */
#E6C200: #D4AF37; /* Golden accent color */
$light-gold: #f1c40f; /* Light golden for highlights */
$background-color: #f5f5f5; /* Light background color */
$card-bg-color: #fff; /* White background for cards */
$card-border-radius: 12px;
$box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow effect */

/* Container for the entire order details page */
.order-details-container {
  max-width: 1200px;
  margin: 30px auto;
  background-color: $card-bg-color;
  padding: 40px;
  border-radius: $card-border-radius;
  box-shadow: $box-shadow;
}

/* Header Section */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #ddd;
  margin-bottom: 30px;
}

.order-title {
  font-size: 2em;
  font-weight: bold;
}

.order-id {
  font-size: 1.2em;
  color: #333;
}

/* Order Summary */
.order-summary {
  margin-bottom: 30px;
}

.order-summary-item {
  font-size: 1.1em;
  margin-bottom: 12px;
  color: #333;
}

.total .total-amount {
  font-size: 1.5em;
  font-weight: bold;
  color:#D4AF37;
}

/* Items Table */
.order-items {
  margin-top: 30px;
}

.order-items-title {
  font-size: 1.8em;
  margin-bottom: 15px;
}

.order-items-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

.order-items-table th,
.order-items-table td {
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.order-items-table th {
  background-color: #fff; /* Golden background for title */
  color: #000;
  font-size: 1.2em;
}

.order-items-table td {
  color: #333;
}

.order-items-table tr:hover {
  background-color: #f8f8f8;
}

/* Action Buttons */
.order-actions {
  margin-top: 30px;
  text-align: center;
}

.back-button {
  background-color: #D4AF37;
  color: white;
  font-size: 1.2em;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.back-button:hover {
  background-color: darken(#D4AF37, 10%);
}

.loading {
  text-align: center;
  font-size: 1.3em;
  color: #888;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #D4AF37; /* Golden color for the spinner */
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
