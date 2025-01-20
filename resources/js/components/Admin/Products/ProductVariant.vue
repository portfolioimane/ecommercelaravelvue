<template>
  <div class="product-selector">
    <div class="form-group">
      <label for="product">Select Product:</label>
      <select id="product" v-model="selectedProductId" @change="fetchVariantsAndValues" class="form-control">
        <option v-for="product in products" :key="product.id" :value="product.id">
          {{ product.name }}
        </option>
      </select>
    </div>

    <!-- Create New Product Variant Button (shown only after product is selected) -->
    <div v-if="selectedProductId" class="create-variant-button">
      <button class="btn btn-success" @click="createNewProductVariant">Create New Product Variant</button>
    </div>

    <div class="product-variants">
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
              <button class="btn btn-danger" @click="deleteProductVariantNow(productvariant.id)">Delete</button>
            </td>
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
      selectedProductId: null,
    };
  },

  computed: {
    ...mapGetters('backendProducts', ['allProducts']),
    products() {
      return this.allProducts;
    },
    productvariants() {
      return this.$store.getters['backendProductVariant/getProductVariants'];
    }
  },

  methods: {
    ...mapActions('backendProducts', ['fetchProducts']),
    ...mapActions('backendProductVariant', ['fetchProductVariants', 'updateProductVariant', 'deleteProductVariant']),

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

    // Delete method for a product variant
    deleteProductVariantNow(id) {
      if (confirm('Are you sure you want to delete this product variant?')) {
        this.deleteProductVariant(id);
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
    // Watch for route changes (if needed to refetch)
    $route() {
      this.fetchVariantsAndValues();
    }
  },

  mounted() {
    this.fetchProducts(); // Fetch products when the component is mounted
  },

  created() {
    this.fetchProducts(); // Ensure products are fetched when the component is created
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
</style>
