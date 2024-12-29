<template>
  <div class="edit-product-container">
    <h1>Edit Product</h1>
    <form @submit.prevent="submitForm" class="product-form">
      <div class="form-group">
        <label for="name">Product Name:</label>
        <input
          type="text"
          id="name"
          v-model="product.name"
          required
          placeholder="e.g., Product Name"
        />
      </div>

      <div class="form-group">
        <label for="description">Description:</label>
        <textarea
          id="description"
          v-model="product.description"
          required
          placeholder="Describe the product"
        ></textarea>
      </div>

      <div class="form-group">
        <label for="price">Price (in MAD):</label>
        <input
          type="number"
          id="price"
          v-model="product.price"
          required
          placeholder="e.g., 500"
          min="0"
        />
      </div>

      <div class="form-group">
        <label for="stock">Stock Quantity:</label>
        <input
          type="number"
          id="stock"
          v-model="product.stock"
          required
          placeholder="e.g., 100"
          min="1"
        />
      </div>

      <div class="form-group">
        <label for="category_id">Category:</label>
        <select id="category_id" v-model="product.category_id" required>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label for="image">Upload New Image (optional):</label>
        <input
          type="file"
          id="image"
          @change="handleImageUpload"
          accept="image/*"
        />
        <small class="help-text">
          <span>For optimal performance, please upload compressed images with a maximum size of 2MB. Consider using a tool to reduce the file size before uploading.</span>
        </small>
        <!-- Display error message if the image is too large -->
        <p v-if="imageError" class="error-message">The image file size exceeds the 2MB limit. Please upload a smaller image.</p>

        <!-- Image preview -->
        <div v-if="imagePreview" class="image-preview mt-2">
          <img :src="imagePreview" alt="Image Preview" class="img-fluid img-preview" />
        </div>
      </div>

      <div class="button-group">
        <button type="submit" class="btn primary">Update Product</button>
        <router-link to="/admin/products" class="btn secondary">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
  data() {
    return {
      product: {
        id: null,
        name: '',
        description: '',
        price: '',
        stock: '',
        category_id: '',
        image: null,
      },
      imagePreview: null, // To store image preview URL
      imageError: false,  // Flag to track image validation error
    };
  },
  computed: {
    ...mapGetters('backendCategories', ['allCategories']),
    categories() {
      return this.allCategories;
    },
    ...mapGetters('backendProducts', ['currentProduct']),
  },
  methods: {
    ...mapActions('backendCategories', ['fetchCategories']),

    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        // Check if file size exceeds 2MB (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
          this.imageError = true;  // Set error flag to true
          this.product.image = null;  // Reset the image if it exceeds the size limit
          this.imagePreview = null;  // Reset preview if the image is invalid
        } else {
          this.imageError = false;  // No error if file is valid
          this.product.image = file;  // Set the image if it's valid
          
          // Create a preview URL for the image
          this.imagePreview = URL.createObjectURL(file); // Generate the preview URL
        }
      }
    },

    async submitForm() {
      if (this.imageError) {
        // Prevent form submission if there's an image error
        alert("Please upload a valid image.");
        return;
      }

      const formData = new FormData();
      formData.append('name', this.product.name);
      formData.append('description', this.product.description);
      formData.append('price', this.product.price);
      formData.append('stock', this.product.stock);
      formData.append('category_id', this.product.category_id);
      
      if (this.product.image) {
        formData.append('image', this.product.image);
      }

      await this.$store.dispatch('backendProducts/updateProduct', { id: this.$route.params.id, formData });
      this.$router.push('/admin/products');
    },

    async fetchProduct() {
      const productId = this.$route.params.id;
      await this.$store.dispatch('backendProducts/fetchProductById', productId);
      this.product = { ...this.currentProduct };
      this.imagePreview = this.product.image ? `/storage/${this.product.image}` : null; // Set image preview if already exists
    },
  },
  mounted() {
    this.fetchProduct();
    this.fetchCategories(); // Fetch categories on component mount
  },
};
</script>

<style scoped>
.edit-product-container {
  margin: 1.5rem auto;
  max-width: 600px;
  color: #333;
}

.edit-product-container h1 {
  color: #d4af37;
  font-size: 1.6rem;
  font-weight: bold;
  margin-bottom: 1rem;
  text-align: center;
}

.product-form .form-group {
  margin-bottom: 0.5rem;
}

.product-form label {
  display: block;
  font-weight: bold;
  margin-bottom: 0.25rem;
  color: #555;
}

.product-form input[type="text"],
.product-form input[type="number"],
.product-form input[type="file"],
.product-form select,
.product-form textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 0.9rem;
}

.product-form input:focus,
.product-form select:focus,
.product-form textarea:focus {
  border-color: #d4af37;
  outline: none;
}

.product-form textarea {
  resize: vertical;
  min-height: 80px;
}

.button-group {
  display: flex;
  justify-content: space-between;
  margin-top: 1rem;
}

.btn {
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  text-transform: uppercase;
}

.btn.primary {
  background-color: #d4af37;
  color: #fff;
}

.btn.secondary {
  background-color: #555;
  color: #fff;
}

.btn:hover {
  opacity: 0.9;
}

.help-text {
  font-size: 0.85rem;
  color: #777;
  margin-top: 0.5rem;
}

.error-message {
  color: red;
  font-size: 0.85rem;
  margin-top: 0.5rem;
}
.img-preview{
  width:100px !important;
  height:100px !important;
}
@media (max-height: 768px) {
  .edit-product-container {
    margin: 1rem auto;
  }

  .product-form input,
  .product-form textarea {
    padding: 0.4rem;
    font-size: 0.85rem;
  }

  .btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
  }
}
</style>
