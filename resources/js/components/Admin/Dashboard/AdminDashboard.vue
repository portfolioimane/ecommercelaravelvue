<template>
  <div class="dashboard">
    <h1>Admin Dashboard</h1>

    <!-- Dashboard Stats Section -->
    <div class="stats">
      <div class="stat-card">
        <h3>Total Products</h3>
        <p>{{ productsCount }}</p>
      </div>
      <div class="stat-card">
        <h3>Total Orders</h3>
        <p>{{ totalOrders }}</p>
      </div>
      <div class="stat-card">
        <h3>Total Categories</h3>
        <p>{{ totalCategories }}</p>
      </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="recent-orders">
      <h2>Recent Orders</h2>
      <table>
        <thead>
          <tr>
            <th>Customer Name</th>
            <th>Date</th>
            <th>Total Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in recentOrders" :key="order.id">
            <td>{{ order.name }}</td>
            <td>{{ formatDate(order.created_at) }}</td>
            <td>{{ order.total }} MAD</td>
            <td>{{ order.status }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  name: "Dashboard",
  computed: {
    ...mapGetters({
      products: "backendProducts/allProducts",
      orders: "backendOrders/orders",
      categories: "backendCategories/allCategories",
    }),

    // Calculate the total number of products
    productsCount() {
      return this.products.length;
    },

    totalOrders() {
      return this.orders.length;
    },

    totalCategories() {
      return this.categories.length;
    },

    // Get the most recent 3 orders
    recentOrders() {
      return [...this.orders]
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at)) // Sort orders by date
        .slice(0, 3); // Limit to the most recent 3 orders
    },
  },
  methods: {
    async fetchDashboardData() {
      try {
        // Fetch products, orders, and categories data
        await this.$store.dispatch("backendProducts/fetchProducts");
        await this.$store.dispatch("backendOrders/fetchOrders");
        await this.$store.dispatch("backendCategories/fetchCategories");
      } catch (error) {
        console.error("Error fetching dashboard data:", error);
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString("en-US"); // Format date
    },
  },
  mounted() {
    this.fetchDashboardData(); // Fetch data when the component is mounted
  },
};
</script>

<style scoped>
.dashboard {
  padding: 20px;
}

.stats {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.stat-card {
  flex: 1;
  background-color: var(--primary-color);
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  border: 1px solid #ddd;
  transition: transform 0.3s ease, background-color 0.3s ease;
}

.stat-card:hover {
  background-color: var(--primary-color);
  transform: scale(1.05);
}

.stat-card h3 {
  margin: 0;
  color: var(--secondary-color);
}

.stat-card p {
  font-size: 24px;
  margin-top: 10px;
  font-weight: bold;
}

.recent-orders {
  margin-top: 20px;
}

.recent-orders table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

.recent-orders th,
.recent-orders td {
  padding: 12px;
  border: 1px solid #ddd;
  text-align: left;
}

.recent-orders th {
  background-color: #f4f4f4;
}
</style>
