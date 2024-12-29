<template>
  <div class="categories">
    <h1>Manage Categories</h1>

    <!-- Add Category Form -->
    <div class="add-category">
      <input v-model="newCategory.name" placeholder="Enter category name" />
      <button class="btn primary" @click="addCategory">Add Category</button>
    </div>

    <!-- Inline Feedback Message -->
    <div v-if="message" :class="message.type" class="inline-message">
      {{ message.text }}
    </div>

    <!-- Categories Table -->
    <table class="categories-table">
      <thead>
        <tr>
          <th>Category ID</th>
          <th>Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="category in categories" :key="category.id">
          <td>{{ category.id }}</td>
          <td>{{ category.name }}</td>
          <td>
            <button class="btn danger" @click="openDeleteModal(category.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Compact Delete Modal -->
    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal-content">
        <h3>Are you sure you want to delete this category?</h3>
        <div class="modal-actions">
          <button class="btn danger" @click="deleteCategory">Yes, Delete</button>
          <button class="btn primary" @click="closeDeleteModal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'Categories',
  data() {
    return {
      newCategory: { name: '' },
      showDeleteModal: false,
      categoryToDelete: null,
      message: null,
    };
  },
  computed: {
    ...mapGetters('backendCategories', ['allCategories']),
    categories() {
      return this.allCategories;
    },
  },
  methods: {
    async addCategory() {
      if (this.newCategory.name) {
        try {
          await this.$store.dispatch('backendCategories/addCategory', this.newCategory);
          this.newCategory.name = ''; // Reset after adding
          this.showMessage('Category added successfully', 'success');
        } catch (error) {
          this.showMessage('Error adding category', 'error');
        }
      } else {
        this.showMessage('Please enter a category name', 'warning');
      }
    },

    openDeleteModal(categoryId) {
      this.categoryToDelete = categoryId;
      this.showDeleteModal = true;
    },

    closeDeleteModal() {
      this.showDeleteModal = false;
      this.categoryToDelete = null;
    },

    async deleteCategory() {
      try {
        await this.$store.dispatch('backendCategories/deleteCategory', this.categoryToDelete);
        this.showMessage('Category deleted successfully', 'success');
        this.showDeleteModal = false;
        this.categoryToDelete = null;
      } catch (error) {
        this.showMessage('Error deleting category', 'error');
        console.error('Error deleting category:', error);
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
    this.$store.dispatch('backendCategories/fetchCategories');
  },
};
</script>

<style scoped>
.categories {
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
  color: #D4AF37;
  margin-bottom: 20px;
  text-align: center;
}

.categories-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.categories-table th,
.categories-table td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: left;
}

.categories-table th {
  background-color: #D4AF37;
  color: white;
}

.categories-table tbody tr:nth-child(even) {
  background-color: #f2f2f2;
}

.add-category {
  margin-top: 20px;
  text-align: center;
}

.add-category input {
  padding: 10px;
  font-size: 1rem;
  border-radius: 5px;
  border: 1px solid #ddd;
  margin-right: 10px;
  width: 300px;
}

.add-category button {
  padding: 10px 15px;
  background-color: #D4AF37;
  border: none;
  border-radius: 5px;
  color: white;
  cursor: pointer;
}

.add-category button:hover {
  opacity: 0.9;
}

.btn {
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
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

/* Inline Message Styles */
.inline-message {
  margin-top: 20px;
  padding: 10px;
  border-radius: 5px;
  text-align: center;
  font-weight: bold;
  transition: opacity 0.5s ease-in-out;
}

.inline-message.success {
  background-color: #28a745;
  color: white;
}

.inline-message.error {
  background-color: #e74c3c;
  color: white;
}

.inline-message.warning {
  background-color: #f39c12;
  color: white;
}
</style>
