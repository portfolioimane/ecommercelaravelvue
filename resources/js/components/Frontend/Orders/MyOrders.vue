<template>
  <div v-if="isLoading" class="loading-state">
    <p>Loading orders...</p>
  </div>
  <div v-else>
    <h2>Your Orders</h2>
    <div v-if="orders.length === 0">
      <p>No orders found.</p>
    </div>
    <div v-else>
      <table class="orders-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Total (MAD)</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
            <th>Status</th>
            <th>Payment Method</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id">
            <td>{{ order.id }}</td>
            <td>{{ formatDate(order.created_at) }}</td>
            <td>{{ order.total }} MAD</td>
            <td>{{ order.phone }}</td>
            <td>{{ order.email }}</td>
            <td>{{ order.address }}</td> <!-- Added the address field here -->
            <td>{{ order.status }}</td>
            <td>{{ order.payment_method }}</td>
            <td><button @click="viewOrderDetails(order.id)">View Details</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  data() {
    return {
      isLoading: true,
    };
  },
  computed: {
    ...mapGetters("orders", ["allOrders"]),
    orders() {
      return this.allOrders;
    },
  },
  methods: {
    ...mapActions("orders", ["fetchMyOrders"]),
    async viewOrderDetails(id) {
      try {
        const order = await this.$store.dispatch("orders/fetchOrderById", id);
        console.log("Order details:", order);
        // Navigate to order details page (if needed)
        this.$router.push({ name: "OrderDetails", params: { id } });
      } catch (error) {
        console.error("Error fetching order details:", error);
      }
    },
    formatDate(date) {
      const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
      return new Date(date).toLocaleDateString('en-US', options);
    }
  },
  async mounted() {
    await this.fetchMyOrders();
    this.isLoading = false;
  },
};
</script>

<style scoped>
.orders-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.orders-table th,
.orders-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.orders-table th {
  background-color: #f4f4f4;
}

.orders-table button {
  background-color: #D4AF37;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.orders-table button:hover {
  background-color: #B78B2C;
}

.loading-state {
  text-align: center;
  font-size: 18px;
  color: #666;
}
</style>
