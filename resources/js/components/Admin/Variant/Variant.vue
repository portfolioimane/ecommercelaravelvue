<template>
  <div class="variant-list-container">
    <h2 class="section-title">Variants and Their Values</h2>

    <table class="variant-table">
      <thead>
        <tr>
          <th>Variant Name</th>
          <th>Variant Type</th>
          <th>Variant Values</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="variant in variants" :key="variant.id">
          <td>{{ variant.name }}</td>
          <td>{{ variant.type }}</td>
          <td>
            <div class="variant-values-list">
              <span 
                v-for="value in variant.variant_values" 
                :key="value.id" 
                class="variant-value-tag"
                :style="getTagStyle(value.value)"
              >
                <span 
                  class="color-preview" 
                  :style="getColorPreviewStyle(value.value)"
                ></span>
                {{ value.value }}
                <button 
                  class="btn-close"
                  @click="deleteVariantValue(variant.id, value.id)"
                >
                  &times;
                </button>
              </span>
            </div>
            <button 
              class="btn btn-small-orange" 
              @click="goToCreateVariantValue(variant.id)"
            >
              Add Variant Value
            </button>
          </td>
          <td>
            <button 
              class="btn btn-small-danger" 
              @click="deleteVariant(variant.id)"
            >
              Delete Variant
            </button>
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
      variants: [],
    };
  },
  created() {
    this.fetchVariants();
  },
  methods: {
    async fetchVariants() {
      try {
        await this.$store.dispatch('backendVariant/fetchVariants');
        this.variants = this.$store.getters['backendVariant/getVariants'];
      } catch (error) {
        console.error('Error fetching variants:', error);
      }
    },
    // Redirect to the Create Variant Value page
    goToCreateVariantValue(variantId) {
      this.$router.push(`/admin/variant/${variantId}/createvariantvalue`);
    },
    // Delete Variant Value
    async deleteVariantValue(variantId, valueId) {
      try {
        await this.$store.dispatch('backendVariant/deleteVariantValue', { variantId, valueId });
        this.fetchVariants(); // Refresh the list after deletion
      } catch (error) {
        console.error('Error deleting variant value:', error);
      }
    },
    // Delete Variant
    async deleteVariant(variantId) {
      try {
        await this.$store.dispatch('backendVariant/deleteVariant', variantId);
        this.fetchVariants(); // Refresh the list after deletion
      } catch (error) {
        console.error('Error deleting variant:', error);
      }
    },
    // Return the appropriate style for color values
    getTagStyle(value) {
      // Check if the value is a valid hex color code
      const hexColorPattern = /^#[0-9A-F]{6}$/i;
      return hexColorPattern.test(value)
        ? { backgroundColor: value, color: this.getContrastingTextColor(value) }
        : {};
    },
    // Get contrasting text color (light or dark) for better readability
    getContrastingTextColor(color) {
      const rgb = parseInt(color.slice(1), 16); // Convert hex to RGB
      const r = (rgb >> 16) & 0xff;
      const g = (rgb >>  8) & 0xff;
      const b = (rgb >>  0) & 0xff;
      const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;
      return luminance > 128 ? 'black' : 'white';
    },
    // Return style for the color preview icon
    getColorPreviewStyle(value) {
      // If it's a valid color, show the preview background as the color
      const hexColorPattern = /^#[0-9A-F]{6}$/i;
      return hexColorPattern.test(value)
        ? { backgroundColor: value }
        : {};
    }
  },
};
</script>

<style scoped>
.variant-list-container {
  padding: 30px;
  max-width: 1000px;
  margin: 0 auto;
  background-color: #ffffff;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}

.section-title {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 20px;
  color: #333;
}

.variant-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.variant-table th,
.variant-table td {
  padding: 15px;
  text-align: left;
  border: 1px solid #ddd;
}

.variant-table th {
  background-color: #f4f4f4;
  font-weight: 700;
}

.variant-table tr:nth-child(even) {
  background-color: #f9f9f9;
}

.variant-values-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px; /* Add space between items */
  margin-bottom: 10px;
}

.variant-value-tag {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  background-color: #007bff; /* Default blue background */
  color: white;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
}

.variant-value-tag button {
  background: none;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
  margin-left: 8px;
}

.variant-value-tag button:hover {
  color: #ff4d4d; /* Red color for hover effect */
}

/* Color preview circle */
.color-preview {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  margin-right: 8px;
}

/* Small Orange Button Style */
.btn-small-orange {
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 600;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  background-color: #ff8c00; /* Orange color */
  color: white;
  margin-top: 10px;
}

.btn-small-orange:hover {
  background-color: #e67600; /* Darker orange on hover */
}

/* Delete Button Style */
.btn-small-danger {
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 600;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  background-color: #e74c3c; /* Red color */
  color: white;
  margin-top: 10px;
}

.btn-small-danger:hover {
  background-color: #c0392b; /* Darker red on hover */
}
</style>
