<template>
  <div class="order-details-container">
    <h1 class="title">Order Details</h1>

    <!-- Print Button -->
    <div class="print-button">
      <button @click="printOrderDetails">Print Order Details</button>
    </div>

    <!-- Display basic order information -->
    <div class="order-info" v-if="order">
      <p><strong>Order ID:</strong> {{ order.id }}</p>
      <p><strong>Customer Name:</strong> {{ order.name }}</p>
      <p><strong>Email:</strong> {{ order.email }}</p>
      <p><strong>Phone:</strong> {{ order.phone }}</p>
      <p><strong>Address:</strong> {{ order.address }}</p>
      <p><strong>Payment Method:</strong> {{ order.payment_method }}</p>
      <p><strong>Total:</strong> {{ order.total }}</p>
      <p><strong>Status:</strong> {{ order.status }}</p>
    </div>

    <!-- Display order items in a table -->
    <h2 class="items-title" v-if="order">Order Items</h2>
    <table class="order-items-table" v-if="order">
      <thead>
        <tr>
          <th>Product Name</th>
          <th>Image</th>

          <th>Variant</th>
          <th>Quantity</th>
          <th>Price</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in order.order_items" :key="item.id">
          <td>{{ item.product.name }}</td>
          <td>
  <img 
    :src="item.variant && item.variant.image ? `/${item.variant.image}` : `/${item.product.image}`" 
    alt="Product Image" 
    width="50" 
    height="50" 
    class="product-image"
  />
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
          <td>{{ item.quantity }}</td>
<td>
  ${{ item.variant ? item.variant.price : item.product.price }}
</td>
     <td>${{ ((item.variant ? item.variant.price : item.product.price) * item.quantity).toFixed(2) }}</td>       
   </tr>
      </tbody>
    </table>

    <!-- Back button to navigate to orders list -->
    <button @click="goBack" class="btn-golden">Back to Orders</button>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  data() {
    return {
      orderId: this.$route.params.id, // Get order ID from route params
    };
  },
  computed: {
    ...mapState('backendOrders', ['currentOrder']), // Map current order from Vuex state
    order() {
      return this.currentOrder;
    },
  },
  methods: {
    // Fetch order details when the component is created
    fetchOrderDetails() {
      this.$store.dispatch('backendOrders/fetchOrderDetails', this.orderId)
        .catch((error) => {
          console.error('Error fetching order details:', error);
        });
    },

    // Go back to the orders list
    goBack() {
      this.$router.push({ name: 'Orders' });
    },

    // Print only the order details section
    printOrderDetails() {
      const printContents = document.querySelector('.order-details-container').innerHTML;
      const originalContents = document.body.innerHTML;

      document.body.innerHTML = printContents; // Set the content to print section only
      window.print(); // Trigger print
      document.body.innerHTML = originalContents; // Restore the original page content
    },
  },
  created() {
    this.fetchOrderDetails(); // Fetch order details when the component is created
  },
};
</script>

<style scoped>
.order-details-container {
  padding: 20px;
}

.title {
  font-size: 24px;
  margin-bottom: 20px;
}

.order-info p {
  margin: 10px 0;
}

.items-title {
  font-size: 20px;
  margin-top: 20px;
}

.order-items-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.order-items-table th,
.order-items-table td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: center;
}

.order-items-table th {
  background-color: #f4f4f4;
}

button {
  padding: 8px 12px;
  background-color: #007bff;
  color: white;
  border: none;
  cursor: pointer;
  margin-top: 20px;
}

button:hover {
  background-color: #0056b3;
}

.color-circle {
  display: inline-block;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  border: 1px solid #ccc;
  margin-left: 5px;
}

/* Print Media Query */
@media print {
  /* Hide everything except the order details section */
  body * {
    visibility: hidden;
  }
  .order-details-container, .order-details-container * {
    visibility: visible;
  }
  .order-details-container {
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>
