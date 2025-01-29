<template>
  <div class="products">
    <h1>Manage Products</h1>
    
    <table class="products-table">
      <thead>
        <tr>
          <th>Product ID</th>
          <th>Name</th>
          <th>Description</th>
          <th>Price (MAD)</th>
          <th>Stock</th>
          <th>Category</th>
          <th>Image</th>
          <th>Featured</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>{{ product.id }}</td>
          <td>{{ product.name }}</td>
          <td>{{ product.description }}</td>
          <td>{{ parseFloat(product.price).toFixed(2) }}</td>
          <td>{{ product.stock }}</td>
          <td>{{ product.category.name}}</td>
          <td>
            <img 
              :src="product.image ? `/${product.image}` : '/images/products/default-product.png'" 
              alt="Product Image" 
              class="product-image"
            />
          </td>
<td>
  <input 
    type="checkbox" 
    v-model="product.featured" 
    @change="toggleFeatured(product)" 
  />
</td>


          <td>
            <button class="btn secondary" @click="editProduct(product)">Edit</button>
            <button class="btn danger" @click="openDeleteModal(product.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Compact Delete Modal -->
    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal-content">
        <h3>Are you sure you want to delete this product?</h3>
        <div class="modal-actions">
          <button class="btn danger" @click="deleteProduct">Yes, Delete</button>
          <button class="btn primary" @click="closeDeleteModal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'Products',
  data() {
    return {
      showDeleteModal: false,
      productToDelete: null,
      message: null,
    };
  },
  computed: {
    ...mapGetters('backendProducts', ['allProducts']),
    products() {
      return this.allProducts;
    },
    products() {
      // Normalize is_featured value to boolean
      return this.allProducts.map(product=> ({
        ...product,
        featured: Boolean(product.featured), // Convert to true/false
      }));
    },
  },
  methods: {
    editProduct(product) {
      this.$router.push({ name: 'EditProduct', params: { id: product.id } });
    },
    openDeleteModal(productId) {
      this.productToDelete = productId;
      this.showDeleteModal = true;
    },
    closeDeleteModal() {
      this.showDeleteModal = false;
      this.productToDelete = null;
    },
    async deleteProduct() {
      try {
        await this.$store.dispatch('backendProducts/deleteProduct', this.productToDelete);
        this.showDeleteModal = false;
        this.productToDelete = null;
        this.showMessage('Product deleted successfully', 'success');
      } catch (error) {
        this.showMessage('Error deleting product', 'error');
        console.error('Error deleting product:', error);
      }
    },
async toggleFeatured(product) {
  try {
    // Dispatch toggleFeatured action with the product ID
    await this.$store.dispatch('backendProducts/toggleFeatured', product.id);
        this.$store.dispatch('backendProducts/fetchProducts');
        console.log('allproducts', this.products);
        this.showMessage('Product featured status updated', 'success');
  } catch (error) {
    this.showMessage('Error updating featured status', 'error');
    console.error('Error updating featured status:', error);
  }
},
    showMessage(text, type) {
      this.message = { text, type }; // Show message with type (success, error, warning)
      setTimeout(() => {
        this.message = null;  // Hide message after 5 seconds
      }, 5000);
    }
  },
  mounted() {
    this.$store.dispatch('backendProducts/fetchProducts');
  },
};
</script>

<style scoped>
/* (Keep the existing styles) */

.products {
  padding: 30px;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
  color: #D4AF37;
  margin-bottom: 20px;
  text-align: center;
  font-size: 2rem;
  font-weight: bold;
}

.products-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.products-table th,
.products-table td {
  border: 1px solid #ddd;
  padding: 15px;
  text-align: left;
}

.products-table th {
  background-color: #D4AF37;
  color: white;
  font-weight: bold;
}

.products-table tbody tr:nth-child(even) {
  background-color: #f2f2f2;
}

.products-table tbody tr:hover {
  background-color: #f9f9f9;
}

.product-image {
  max-width: 100px;
  max-height: 100px;
  object-fit: cover;
}

.btn {
  padding: 12px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
  font-weight: bold;
}

.btn.secondary {
  background-color: #f39c12;
  color: white;
}

.btn.danger {
  background-color: #e74c3c;
  color: white;
}

.btn.primary {
  background-color: #D4AF37;
  color: white;
}

.btn:hover {
  opacity: 0.9;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 100%;
  text-align: center;
}

.modal-actions {
  margin-top: 20px;
}

.modal-actions button {
  margin: 0 10px;
}
.product-image{
  width:100px !important;
  height:100px !important;
}
</style>
