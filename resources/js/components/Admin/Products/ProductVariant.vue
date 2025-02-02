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

    <!-- Information Message for Existing Variants -->
    <div v-if="selectedProductId && productvariants.length > 0" class="info-message-container">
      <p class="info-message">If you want to create new product variants, you must delete all existing variants first. Once deleted, you can create all product variants again.</p>
      <button class="btn btn-danger" @click="openModal('deleteAll')">Delete All Variants</button>
    </div>

    <!-- Button to Create Variants -->
    <div v-if="selectedProductId && productvariants.length === 0" class="create-variant-button">
      <p class="info-message">Make sure you create all product variants you need because if you want to do it again, you will have to delete all variants and create them again.</p>
      <button class="btn btn-success" @click="createNewProductVariant">Create All Product Variants</button>
    </div>

    <!-- Variants Table -->
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
              <button class="btn btn-warning" @click="editProductVariant(productvariant)">Update</button>
              <button class="btn btn-danger" @click="openModal('delete', productvariant.id)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- No Variants Message -->
    <div v-if="selectedProductId && productvariants.length === 0" class="no-variants">
      <p>No variants available for this product.</p>
    </div>

    <!-- Modal for Delete Confirmation & Warnings -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <h3>{{ modalMessage }}</h3>
        <div class="modal-buttons">
          <button v-if="modalType === 'delete'" class="btn btn-danger" @click="deleteProductVariantNow">Yes, Delete</button>
          <button v-if="modalType === 'deleteAll'" class="btn btn-danger" @click="deleteAllVariantsNow">Yes, Delete All</button>
          <button v-if="modalType === 'warning'" class="btn btn-primary" @click="closeModal">OK</button>
          <button class="btn btn-secondary" @click="closeModal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  data() {
    return {
      selectedProductId: null,
      showModal: false,
      modalMessage: "",
      modalType: "",
      productVariantToDelete: null
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
    ...mapActions('backendProductVariant', ['fetchProductVariants', 'updateProductVariant', 'deleteProductVariant', 'deleteAllProductVariants']),

    fetchVariantsAndValues() {
      if (this.selectedProductId) {
        this.fetchProductVariants(this.selectedProductId);
      }
    },

    getImagePath(image) {
      return `/${image}`;
    },

    createNewProductVariant() {
      if (!this.selectedProductId) {
        this.openModal("warning", null, "Please select a product first.");
      } else {
        this.$router.push(`/admin/createproductvariant/${this.selectedProductId}`);
      }
    },

    openModal(type, id = null, message = null) {
      this.modalType = type;
      this.productVariantToDelete = id;
      this.modalMessage =
        message ||
        (type === "delete"
          ? "Are you sure you want to delete this product variant?"
          : type === "deleteAll"
          ? "Are you sure you want to delete all variants for this product?"
          : "");

      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
      this.productVariantToDelete = null;
    },

    deleteProductVariantNow() {
      if (this.productVariantToDelete) {
        this.deleteProductVariant(this.productVariantToDelete);
        this.closeModal();
      }
    },

    deleteAllVariantsNow() {
      if (this.selectedProductId) {
        this.deleteAllProductVariants(this.selectedProductId);
        this.closeModal();
      }
    }
  },

  mounted() {
    this.fetchProducts();
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

.info-message {
  font-size: 14px;
  color: #f44336;
  margin-bottom: 10px;
  font-weight: bold;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th, .table td {
  padding: 12px;
  border: 1px solid #ddd;
}

.variant-image {
  width: 50px;
  height: 50px;
  object-fit: cover;
}

.color-circle {
  display: inline-block;
  width: 20px;
  height: 20px;
  border-radius: 50%;
}

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

.btn {
  padding: 6px 12px;
  margin: 5px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}

.btn-warning { background-color: #ff9800; color: white; }
.btn-danger { background-color: #f44336; color: white; }
.btn-success { background-color: #4caf50; color: white; }
.btn-primary { background-color: #007bff; color: white; }
.btn-secondary { background-color: #6c757d; color: white; }
.btn:hover { opacity: 0.8; }
</style>
