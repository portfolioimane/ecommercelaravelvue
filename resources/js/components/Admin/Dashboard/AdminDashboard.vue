<template>
  <div class="dashboard">
    <h1>Admin Dashboard</h1>

    <!-- Dashboard Stats Section -->
    <div class="stats">
      <div class="stat-card">
        <h3>Total Products</h3>
        <p>{{ productsCount }}</p> <!-- Display the total product count -->
      </div>
      <div class="stat-card">
        <h3>Total Orders</h3>
        <p>{{ totalOrders }}</p> <!-- Display the total orders count -->
      </div>
      <div class="stat-card">
        <h3>Total Customers</h3>
        <p>{{ totalCustomers }}</p> <!-- Display the total customers count -->
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
            <td>{{ order.customerName }}</td>
            <td>{{ order.date }}</td>
            <td>{{ order.totalAmount }}</td>
            <td>{{ order.status }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "Dashboard",
  data() {
    return {
      productsCount: 0,           // Total number of products
      totalOrders: 0,             // Total number of orders
      totalCustomers: 0,          // Total number of customers
      orders: [],                 // Store all fetched orders
    };
  },
  computed: {
    // Get the three latest orders
    recentOrders() {
      return this.orders
        .sort((a, b) => new Date(b.date) - new Date(a.date)) // Sort by date (latest first)
        .slice(0, 3); // Get the 3 most recent orders
    },
  },
  methods: {
    async fetchDashboardData() {
      try {
        // Fetch total products data
        const products = await this.$store.dispatch("products/fetchProducts");
        this.productsCount = products.length;

        // Fetch total orders data
        const orders = await this.$store.dispatch("orders/fetchOrders");
        this.orders = orders;
        this.totalOrders = orders.length;

        // Fetch total customers data
        const customers = await this.$store.dispatch("customers/fetchCustomers");
        this.totalCustomers = customers.length;
      } catch (error) {
        console.error("Error fetching dashboard data:", error);
      }
    },
  },
  mounted() {
    this.fetchDashboardData(); // Fetch data on component mount
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
  transition: transform 0.3s ease, background-color 0.3s ease; /* Smooth transition for scale and color */
}

.stat-card:hover {
  background-color: var(--primary-color) !important; /* Keeps background color same on hover */
  transform: scale(1.05); /* Slight scale effect */
}


.stat-card h3 {
  margin: 0;
  color:  var(--secondary-color);
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
