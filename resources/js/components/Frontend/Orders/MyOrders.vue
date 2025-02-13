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
            <td>{{ order.address }}</td>
            <td>{{ order.status }}</td>
            <td>{{ order.payment_method }}</td>
            <td>
              <button @click="viewOrderDetails(order.id)">View Details</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <div class="pagination">
        <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage >= totalPages">Next</button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  data() {
    return {
      isLoading: true,
      currentPage: 1,
      totalPages: 1,
    };
  },
  computed: {
    ...mapGetters("orders", ["allOrders", "orderCount"]),
    orders() {
      return this.allOrders;
    },
  },
  methods: {
    ...mapActions("orders", ["fetchPaginatedOrders"]),
    
    async fetchOrders() {
      this.isLoading = true;
      try {
        await this.fetchPaginatedOrders({ page: this.currentPage, perPage: 5 });
        this.totalPages = Math.ceil(this.$store.getters["orders/orderCount"] / 5);
      } catch (error) {
        console.error("Error fetching orders:", error);
      }
      this.isLoading = false;
    },

    async viewOrderDetails(id) {
      try {
        const order = await this.$store.dispatch("orders/fetchOrderById", id);
        console.log("Order details:", order);
        this.$router.push({ name: "OrderDetails", params: { id } });
      } catch (error) {
        console.error("Error fetching order details:", error);
      }
    },

    formatDate(date) {
      const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      };
      return new Date(date).toLocaleDateString("en-US", options);
    },

    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.fetchOrders();
      }
    },

    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        this.fetchOrders();
      }
    },
  },
  async mounted() {
    await this.fetchOrders();
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
  background-color: var(--primary-color);
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.orders-table button:hover {
  background-color: #B78B2C;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
}

.pagination button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 8px 12px;
  margin: 0 5px;
  cursor: pointer;
}

.pagination button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.loading-state {
  text-align: center;
  font-size: 18px;
  color: #666;
}
</style>
