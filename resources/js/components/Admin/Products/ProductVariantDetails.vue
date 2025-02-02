<template>
  <div class="product-selector">
    <div class="product-info-container">
      <h1 class="product-name">{{ currentProduct.name }}</h1>
      <p class="product-description">{{ currentProduct.description }}</p>
    </div>

    <!-- Display product variants table only if a product is selected -->
    <div v-if="selectedProductId && productvariants.length" class="product-variants">
      <h2>Product Variants</h2>
      <table class="table">
        <thead>
          <tr>
            <th>Combination</th>
            <th>Price</th>
            <th>Image</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="productvariant in productvariants" :key="productvariant.id">
            <td>
              <div class="combination-values">
                <div v-for="(value, key) in productvariant.combination_values" :key="key" class="combination-item">
                  <span>{{ key }}: </span>
                  <span v-if="key === 'color'">
                    <span class="color-circle" :style="{ backgroundColor: value }"></span>
                  </span>
                  <span v-else>{{ value }}</span>
                </div>
              </div>
            </td>
            <td>{{ productvariant.price }}</td>
            <td><img :src="getImagePath(productvariant.image)" alt="Product Variant Image" class="variant-image" /></td>
            <td>
              <button class="btn btn-danger" @click="openDeleteModal(productvariant.id)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Message if no variants are found -->
    <div v-if="selectedProductId && productvariants.length === 0" class="no-variants">
      <p>No variants available for this product.</p>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
      <div class="modal-content" @click.stop>
        <h3>Are you sure you want to delete this product variant?</h3>
        <div class="modal-buttons">
          <button class="btn btn-danger" @click="deleteProductVariantNow">Yes, Delete</button>
          <button class="btn btn-secondary" @click="closeDeleteModal">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Back Button -->
    <button class="btn btn-golden" @click="goBack">Go Back</button>

  </div>
</template>


<script>
import { mapGetters, mapActions } from "vuex";

export default {
  data() {
    return {
      selectedProductId: null,
      showDeleteModal: false,
      productVariantToDelete: null,
    };
  },

  computed: {
    ...mapGetters('backendProducts', ['currentProduct']),
    products() {
      return this.allProducts;
    },
    productvariants() {
      return this.$store.getters['backendProductVariant/getProductVariants'];
    },
    selectedProductName() {
      const product = this.products.find(p => p.id === this.selectedProductId);
      return product ? product.name : 'No product selected';
    }
  },

  methods: {
    ...mapActions('backendProducts', ['fetchProductById']),
    ...mapActions('backendProductVariant', ['fetchProductVariants', 'updateProductVariant', 'deleteProductVariant']),

    goBack() {
    this.$router.push({ name: 'ProductVariant' });
  },

    // Fetch variants when a product is selected
    fetchVariantsAndValues() {
      if (this.selectedProductId) {
        this.fetchProductVariants(this.selectedProductId);
      }
    },

    // Construct image path for variants
    getImagePath(image) {
      return `/${image}`;
    },

    // Open the delete confirmation modal
    openDeleteModal(id) {
      this.productVariantToDelete = id;
      this.showDeleteModal = true;
    },

    // Close the delete confirmation modal
    closeDeleteModal() {
      this.showDeleteModal = false;
      this.productVariantToDelete = null;
    },

    // Delete method for a product variant
    deleteProductVariantNow() {
      if (this.productVariantToDelete) {
        this.deleteProductVariant(this.productVariantToDelete);
        this.closeDeleteModal();
      }
    },

    // Create New Product Variant
    createNewProductVariant() {
      if (this.selectedProductId) {
        // Redirect to the create new product variant page with the selected product ID
        this.$router.push(`/admin/createproductvariant/${this.selectedProductId}`);
      } else {
        alert('Please select a product first.');
      }
    }
  },

  watch: {
    // Watch for route changes (to refetch if a new product ID is in the URL)
    '$route'(to, from) {
      const productId = to.params.id;
      if (productId && productId !== this.selectedProductId) {
        this.selectedProductId = productId;
        this.fetchVariantsAndValues();
      }
    }
  },

  mounted() {
    // Check the route for product ID and set it to selectedProductId if it exists
    const productId = this.$route.params.id;
    if (productId) {
      this.selectedProductId = productId;
      this.fetchVariantsAndValues();
    }

    this.fetchProductById(this.selectedProductId);
  },

  created() {
    this.fetchProductById(this.selectedProductId);
  }
};
</script>

<style scoped>
.product-selector {
  padding: 20px;
  font-family: Arial, sans-serif;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  font-size: 16px;
  font-weight: bold;
}

.product-variants {
  margin-top: 20px;
}

h2 {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 20px;
}

.table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.table th, .table td {
  padding: 12px;
  text-align: left;
  border: 1px solid #ddd;
}

.table th {
  background-color: #f4f4f4;
}

.variant-image {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 5px;
}

.btn {
  padding: 6px 12px;
  margin: 5px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  font-size: 14px;
}

.btn-warning {
  background-color: #ff9800;
  color: white;
}

.btn-danger {
  background-color: #f44336;
  color: white;
}

.btn-success {
  background-color: #4caf50;
  color: white;
}

.btn:hover {
  opacity: 0.8;
}

.table tr:hover {
  background-color: #f9f9f9;
}

/* Style for the color circle */
.color-circle {
  display: inline-block;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  margin-left: 5px;
  border: 1px solid #ddd;
}

/* Horizontal alignment for combination values */
.combination-values {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
}

.combination-item {
  margin-right: 15px;
  display: inline-flex;
  align-items: center;
}

/* Optional additional spacing for combination items */
.combination-item span {
  margin-right: 5px;
}

.no-variants {
  margin-top: 20px;
  font-size: 16px;
  color: #777;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  width: 300px;
}

.modal-buttons {
  margin-top: 20px;
}

.modal-buttons .btn {
  margin: 0 10px;
}
</style>
