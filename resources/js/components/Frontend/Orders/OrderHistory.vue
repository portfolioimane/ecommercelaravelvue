<template>
  <div class="container mt-5">
    <h2>Order History</h2>

    <!-- Display orders -->
    <div v-if="orders.length">
      <ul class="list-group">
        <li
          v-for="order in orders"
          :key="order.id"
          class="list-group-item"
        >
          <h5>Order #{{ order.id }}</h5>
          <p>Date: {{ new Date(order.created_at).toLocaleDateString() }}</p>
          <p>Total: ${{ order.total }}</p>

          <button
            class="btn btn-primary btn-sm"
            @click="viewOrderDetails(order.id)"
          >
            View Details
          </button>

          <!-- Order Items -->
          <ul v-if="orderDetails[order.id]" class="mt-3">
            <li
              v-for="item in orderDetails[order.id]"
              :key="item.id"
              class="list-group-item"
            >
              {{ item.name }} - Qty: {{ item.quantity }} - ${{ item.price }}
            </li>
          </ul>
        </li>
      </ul>
    </div>

    <!-- Empty orders message -->
    <div v-else>
      <p class="alert alert-info text-center">
        You have no orders yet.
      </p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      orderDetails: {}, // Stores details of individual orders
    };
  },
  computed: {
    orders() {
      return this.$store.getters['orders/allOrders'];
    },
    orderItems() {
      return this.$store.getters['orders/allOrderItems'];
    },
  },
  methods: {
    async viewOrderDetails(orderId) {
      // If details are already loaded, toggle visibility
      if (this.orderDetails[orderId]) {
        this.$set(this.orderDetails, orderId, null);
        return;
      }

      // Fetch order details from Vuex store or API
      const items = this.orderItems.filter(
        (item) => item.order_id === orderId
      );

      if (items.length) {
        this.$set(this.orderDetails, orderId, items);
      } else {
        console.warn('No items found for order ID:', orderId);
      }
    },
  },
  mounted() {
    // Fetch orders and items when the component is loaded
    this.$store.dispatch('orders/fetchOrders');
  },
};
</script>

<style scoped>
/* Add specific styles for OrderHistory.vue if needed */
</style>
