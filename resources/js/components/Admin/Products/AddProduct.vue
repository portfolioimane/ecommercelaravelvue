<template>
  <div class="add-product">
    <h1>Add New Product</h1>
    <form @submit.prevent="submitForm" class="product-form">
      <div class="form-group">
        <label for="name">Product Name:</label>
        <input
          type="text"
          id="name"
          v-model="newProduct.name"
          required
          placeholder="e.g., Smart Watch"
        />
      </div>

      <div class="form-group">
        <label for="description">Description:</label>
        <textarea
          id="description"
          v-model="newProduct.description"
          required
          placeholder="Describe the product"
        ></textarea>
      </div>

      <div class="form-group">
        <label for="price">Price (in MAD):</label>
        <input
          type="number"
          id="price"
          v-model="newProduct.price"
          required
          placeholder="e.g., 1200"
          min="0"
        />
      </div>

      <div class="form-group">
        <label for="stock">Stock Quantity:</label>
        <input
          type="number"
          id="stock"
          v-model="newProduct.stock"
          required
          placeholder="e.g., 50"
          min="0"
        />
      </div>

      <div class="form-group">
        <label for="category_id">Category:</label>
        <select id="category_id" v-model="newProduct.category_id" required>
          <option value="" disabled>Select a category</option>
          <option
            v-for="category in categories"
            :key="category.id"
            :value="category.id"
          >
            {{ category.name }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label for="image">Upload Image (Max 2MB):</label>
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
      </div>

      <!-- Preview Section -->
      <div v-if="newProduct.image" class="image-preview">
        <img :src="imagePreview" alt="Product Image Preview" />
      </div>

      <div class="button-group">
        <button type="submit" class="btn primary">Add Product</button>
        <router-link to="/admin/products" class="btn secondary">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      newProduct: {
        name: '',
        description: '',
        price: '',
        stock: '',
        category_id: '',
        image: null,
      },
      imageError: false, // Flag to track image validation error
      imagePreview: '', // URL for image preview
    };
  },
  computed: {
    categories() {
      return this.$store.getters['backendCategories/allCategories'];  // Accessing categories from Vuex store
    },
  },
  methods: {
    handleImageUpload(event) {
      const file = event.target.files[0];  // Handling image upload
      if (file) {
        // Check if file size exceeds 2MB (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
          this.imageError = true; // Set error flag to true
          this.newProduct.image = null; // Reset the image if it exceeds the size limit
          this.imagePreview = ''; // Reset preview if the image is invalid
        } else {
          this.imageError = false; // No error if file is valid
          this.newProduct.image = file; // Set the image if it's valid

          // Create a preview URL for the image
          const reader = new FileReader();
          reader.onload = (e) => {
            this.imagePreview = e.target.result; // Set the preview URL
          };
          reader.readAsDataURL(file); // Read the file as a data URL
        }
      }
    },
    async submitForm() {
      if (this.imageError) {
        // Prevent form submission if there's an image error
        alert("Please upload a valid image.");
        return;
      }

      console.log("Form submission started");
      const formData = new FormData();
      Object.entries(this.newProduct).forEach(([key, value]) => {
        console.log(`Appending ${key}: ${value}`);  // Debugging the form data
        formData.append(key, value);
      });

      try {
        console.log("Dispatching addProduct action with formData:", formData);
        await this.$store.dispatch('backendProducts/addProduct', formData);  // Dispatching action to store
        console.log("Product added successfully");
        this.$router.push('/admin/products');  // Redirect to product list
      } catch (error) {
        console.error("Error adding product:", error);  // Handling error
      }
    },
  },
  mounted() {
    console.log("Component mounted, fetching categories...");
    this.$store.dispatch('backendCategories/fetchCategories');  // Fetch categories on mount
  },
};
</script>

<style scoped>
.image-preview {
  margin-top: 1rem;
  text-align: left;
}

.image-preview img {
  width:100px !important;
  height: 100px !important;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.add-product {
  margin: 1.5rem auto;
  max-width: 600px;
  color: #333;
}

.add-product h1 {
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

@media (max-height: 768px) {
  .add-product {
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
