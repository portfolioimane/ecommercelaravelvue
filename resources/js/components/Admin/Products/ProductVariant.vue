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
<!-- Create New Product Variant Button (shown only when no variants exist) -->
<!-- Informational Message if Variants Exist -->
<div v-if="selectedProductId && productvariants.length > 0" class="info-message-container">
  <p class="info-message">If you want to create new product variants, you must delete all existing variants first. Once deleted, you can create all product variants again.</p>
  <button class="btn btn-danger" @click="deleteAllVariants">Delete All Variants</button>
</div>

<!-- Create New Product Variant Button (shown only when no variants exist) -->
<div v-if="selectedProductId && productvariants.length === 0" class="create-variant-button">
  <p class="info-message">Make sure you create all product variants you need because if you want to do it again, you will have to delete all variants and create them again.</p>
  <button class="btn btn-success" @click="createNewProductVariant">Create All Product Variants</button>
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
              <button class="btn btn-warning" @click="editProductVariant(productvariant)">Update</button>
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

    <!-- Update Product Variant Modal -->
    <div v-if="showUpdateModal" class="modal-overlay" @click="closeUpdateModal">
      <div class="modal-content" @click.stop>
        <h3>Update Product Variant</h3>

        <!-- Price Input -->
        <div class="form-group">
          <label for="price">Price:</label>
          <input type="number" id="price" v-model="updatedPrice" class="form-control" />
        </div>

        <!-- Image Upload -->
        <div class="form-group">
          <label for="image">Image:</label>
          <input 
            type="file" 
            id="image" 
            @change="handleImageUpload" 
            class="form-control" 
            accept="image/*" 
          />
          <div v-if="previewImage" class="image-preview">
            <img :src="previewImage" alt="Selected Image" class="img-thumbnail" />
          </div>
        </div>

        <!-- Modal Buttons -->
        <div class="modal-buttons">
          <button class="btn btn-success" @click="editProductVariantNow">Save Changes</button>
          <button class="btn btn-secondary" @click="closeUpdateModal">Cancel</button>
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
      showDeleteModal: false,
      productVariantToDelete: null,
      showUpdateModal: false,
      productVariantToUpdate: null,
      updatedPrice: null,
      updatedImage: null,
      previewImage: null // For previewing the selected image
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
    ...mapActions('backendProductVariant', ['fetchProductVariants', 'updateProductVariant', 'deleteProductVariant','deleteAllProductVariants']),

    // Fetch variants when a product is selected
    fetchVariantsAndValues() {
      if (this.selectedProductId) {
        this.fetchProductVariants(this.selectedProductId);
      }
    },


      deleteAllVariants() {
    if (this.selectedProductId) {
      if (confirm('Are you sure you want to delete all variants for this product?')) {
        this.deleteAllProductVariants(this.selectedProductId);
      }
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
        this.$router.push(`/admin/createproductvariant/${this.selectedProductId}`);
      } else {
        alert('Please select a product first.');
      }
    },

    // Open the update modal and set initial values
    editProductVariant(productvariant) {
      this.productVariantToUpdate = productvariant;
      this.updatedPrice = productvariant.price;
      this.updatedImage = productvariant.image;
      this.previewImage = this.getImagePath(productvariant.image); // Set preview image
      this.showUpdateModal = true;
    },

    // Close the update modal
    closeUpdateModal() {
      this.showUpdateModal = false;
      this.productVariantToUpdate = null;
      this.updatedPrice = null;
      this.updatedImage = null;
      this.previewImage = null; // Reset preview image
    },

    // Handle image file upload
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.previewImage = e.target.result; // Set image preview
          this.updatedImage = file; // Store the file for upload
        };
        reader.readAsDataURL(file);
      }
    },

    // Update the product variant
    async editProductVariantNow() {
      if (this.productVariantToUpdate) {
        const variantId = this.productVariantToUpdate.id;

        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("price", this.updatedPrice);
        if (this.updatedImage) {
          formData.append("image", this.updatedImage);
      }

        await this.updateProductVariant({
          id: variantId,
          formData,
        });

        this.closeUpdateModal();
      }
    }
  },

  watch: {
    $route() {
      this.fetchVariantsAndValues();
    }
  },

  mounted() {
    this.fetchProducts();
  },

  created() {
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
/* Style for informational message */
/* Style for informational message */
.info-message-container {
  margin-top: 20px;
}

.info-message {
  font-size: 14px;
  color: #f44336;
  margin-bottom: 10px;
  font-weight: bold;
}

.btn-danger {
  background-color: #f44336;
  color: white;
}

.btn-danger:hover {
  opacity: 0.8;
}


</style>
