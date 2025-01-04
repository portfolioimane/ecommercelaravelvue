<template>
  <div class="orders-container">
    <h1 class="title">Orders</h1>

    <!-- Action Buttons for CSV Download and Print -->
    <div class="action-buttons">
      <button class="btn download-btn" @click="downloadCSV">Download CSV</button>
      <button class="btn print-btn" @click="printPage">Print</button>
    </div>

    <!-- Table for displaying orders -->
    <table class="orders-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Order Date</th>
          <th>Customer Name</th>
          <th>Phone</th>
          <th>Address</th>
          <th>Payment Method</th>
          <th>Total</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <td>{{ order.id }}</td>
          <td>{{ new Date(order.created_at).toLocaleDateString() }}</td>
          <td>{{ order.name }}</td>
          <td>{{ order.phone }}</td>
          <td>{{ order.address }}</td>
          <td>{{ order.payment_method }}</td>
          <td>{{ order.total }}</td>
          <td>
            <select v-model="order.status" @change="updateOrderStatus(order)" class="status-dropdown">
              <option value="pending">Pending</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </td>
          <td>
            <button class="btn btn-golden" @click="viewOrderDetails(order.id)">View</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      orders: [],
    };
  },
  methods: {
    // Fetch all orders from the backend
    fetchOrders() {
      this.$store.dispatch('backendOrders/fetchOrders')
        .then(() => {
          this.orders = this.$store.getters['backendOrders/orders'];
        })
        .catch((error) => {
          console.error('Error fetching orders:', error);
        });
    },

    // View details of a specific order
    viewOrderDetails(orderId) {
      this.$store.dispatch('backendOrders/fetchOrderDetails', orderId)
        .then(() => {
          this.$router.push({ name: 'BackendOrderDetails', params: { id: orderId } });
        })
        .catch((error) => {
          console.error('Error fetching order details:', error);
        });
    },

    // Update the order status when the dropdown changes
    updateOrderStatus(order) {
      this.$store.dispatch('backendOrders/updateOrderStatus', {
        orderId: order.id,
        status: order.status,
      })
        .then(() => {
          console.log('Order status updated');
        })
        .catch((error) => {
          console.error('Error updating order status:', error);
        });
    },

    // Download the orders as a CSV
    downloadCSV() {
      const headers = ['Order ID', 'Order Date', 'Customer Name', 'Phone', 'Address', 'Payment Method', 'Total', 'Status'];
      const rows = this.orders.map(order => [
        order.id,
        new Date(order.created_at).toLocaleDateString(),
        order.name,
        order.phone,
        order.address,
        order.payment_method,
        order.total,
        order.status,
      ]);

      let csvContent = "data:text/csv;charset=utf-8," + headers.join(",") + "\n";
      rows.forEach(row => {
        csvContent += row.join(",") + "\n";
      });

      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", "orders.csv");
      document.body.appendChild(link);
      link.click();
    },

    // Print only the orders table
    printPage() {
      const tableContent = document.querySelector('.orders-table').outerHTML;
      const printWindow = window.open('', '', 'width=900,height=700');
      printWindow.document.write(`
        <html>
          <head>
            <title>Print Orders</title>
            <style>
              body {
                font-family: Arial, sans-serif;
                padding: 20px;
              }
              table {
                width: 100%;
                border-collapse: collapse;
              }
              th, td {
                padding: 12px;
                border: 1px solid #ddd;
                text-align: center;
              }
              th {
                background-color: #f4f4f4;
                font-weight: bold;
              }
            </style>
          </head>
          <body>
            ${tableContent}
          </body>
        </html>
      `);
      printWindow.document.close();
      printWindow.print();
      printWindow.close();
    },
  },
  created() {
    this.fetchOrders();
  },
};
</script>

<style scoped>
.orders-container {
  padding: 20px;
}

.title {
  font-size: 28px;
  margin-bottom: 20px;
  font-weight: bold;
  color: #333;
}

.action-buttons {
  margin-bottom: 20px;
  display: flex;
  justify-content: space-between;
}

.btn {
  padding: 10px 20px;
  border-radius: 5px;
  font-size: 14px;
  cursor: pointer;
}

.download-btn {
  background-color: #28a745;
  color: white;
  border: none;
}

.download-btn:hover {
  background-color: #218838;
}

.print-btn {
  background-color: #17a2b8;
  color: white;
  border: none;
}

.print-btn:hover {
  background-color: #138496;
}

.orders-table {
  width: 100%;
  border-collapse: collapse;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.orders-table th,
.orders-table td {
  padding: 12px;
  border: 1px solid #ddd;
  text-align: center;
}

.orders-table th {
  background-color: #f4f4f4;
  font-weight: bold;
}

button {
  padding: 8px 16px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}

.status-dropdown {
  padding: 8px;
  font-size: 14px;
  cursor: pointer;
  border-radius: 4px;
  border: 1px solid #ddd;
}

.status-dropdown:focus {
  outline: none;
  border-color: #007bff;
}

/* Print-specific styles */
@media print {
  /* Hide everything except the table */
  body * {
    visibility: hidden !important;
  }

  .orders-container, .orders-table, .orders-table * {
    visibility: visible;
  }

  /* Ensure the table is positioned correctly */
  .orders-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
  }

  /* Hide action buttons during print */
  .action-buttons {
    display: none;
  }
}
</style>
