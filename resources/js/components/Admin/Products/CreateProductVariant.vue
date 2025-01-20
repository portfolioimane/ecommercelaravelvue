<template>
  <div class="product-variant">
    <h1>Create Product Variant Combinations</h1>
 
  <div class="product-info-container">
    <h1 class="product-name">{{ currentProduct.name }}</h1>
    <p class="product-description">{{ currentProduct.description }}</p>
  </div>

    <!-- Variant Selection -->
    <div v-if="variants.length" class="variant-section">
      <h2>Select Variants</h2>
<div v-if="notificationMessage" class="notification-message">
  <p>{{ notificationMessage }}</p>
</div>



      <div v-for="(variant, index) in selectedVariants" :key="index" class="variant-card">
        <div class="variant-select-wrapper">
          <label>{{ variant.name }}:</label>
          <div class="tags-container">
            <div
              v-for="(value, idx) in selectedVariantValues[variant.id]"
              :key="idx"
              class="tag"
            >
              <span
                v-if="isHexColor(value)"
                :style="{ backgroundColor: value }"
                class="color-preview"
              ></span>
              <span v-else>{{ value }}</span>
              <span class="tag-remove" @click="removeTag(variant.id, value)">×</span>
            </div>
          </div>

          <input
            type="text"
            v-model="selectedVariantValues[variant.id]"
            @focus="openDropdown(variant.id)"
            @blur="closeDropdown"
            placeholder="Select variant values"
            class="variant-input"
            readonly
          />
          <div v-if="dropdownOpen[variant.id]" class="dropdown">
            <ul>
              <li
                v-for="value in variant.variant_values"
                :key="value.id"
                @click="addTag(variant.id, value.value)"
              >
                <span
                  v-if="isHexColor(value.value)"
                  :style="{ backgroundColor: value.value }"
                  class="color-preview"
                ></span>
                <span v-if="!isHexColor(value.value)">{{ value.value }}</span>
              </li>
            </ul>
          </div>
        </div>

        <button @click="removeVariant(index)" class="remove-btn">Remove</button>
      </div>

      <!-- Add New Variant -->
      <div class="form-group">
        <label>Add Variant:</label>
        <select @change="addVariant" v-model="selectedVariantId" class="form-control">
          <option value="">Select a Variant</option>
          <option v-for="variant in variants" :key="variant.id" :value="variant.id">
            {{ variant.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- Variant Combinations -->
    <div v-if="combinationList.length" class="combination-section">
      <h2>Variant Combinations</h2>

    <div v-if="successMessage" class="success-message">
      <p>{{ successMessage }}</p>
    </div>
      <table class="combination-table">
        <thead>
          <tr>
            <th>Combination</th>
            <th>Price</th>
            <th>Image</th>
          </tr>
        </thead>
<tbody>
  <tr v-for="(combination, index) in combinationList" :key="index">
    <td>
      <span class="combination-value">
        <span v-for="(value, key, i) in combination.combinationValues" :key="key">
          <strong>{{ key }}:</strong> 
          <span v-if="key === 'color'" :style="{ backgroundColor: value, width: '20px', height: '20px', borderRadius: '50%', display: 'inline-block', marginLeft: '5px' }"></span>
          <span v-else>{{ value }}</span>
          <span v-if="i < Object.keys(combination.combinationValues).length - 1">, </span> <!-- Add comma between values, but not after the last one -->
        </span>
      </span>
    </td>
    <td>
      <input
        type="number"
        v-model="combination.price"
        class="form-control"
        placeholder="Price"
      />
    </td>
    <td>
      <input
        type="file"
        @change="handleImageUpload($event, index)"
        class="form-control"
      />
    </td>
  </tr>
</tbody>


      </table>
      <button @click="updateAllCombinations" class="btn btn-success update-btn">Update All Combinations</button>
    </div>
  </div>
</template>

<script>
import { reactive } from "vue";
import { mapGetters, mapActions } from "vuex";

export default {
  data() {
    return {
      successMessage: '', // Store the success message
      notificationMessage: '', // Store the notification message
      selectedProductId: null,  // Will be set based on route
      selectedVariantId: '',
      selectedVariants: [],
      dropdownOpen: reactive({}),
      selectedVariantValues: reactive({}),
      combinationList: [],
    };
  },
  computed: {
    ...mapGetters('backendProducts', ['currentProduct']),
    ...mapGetters('backendVariant', ['getVariants']),
    products() {
      return this.allProducts;
    },
    variants() {
      return this.getVariants;
    },
  },
  methods: {
    ...mapActions('backendProducts', ['fetchProductById']),
    ...mapActions('backendVariant', ['fetchVariants']),
    ...mapActions('backendVariantCombinations', ['fetchExistingCombinationValues']), // Added missing action

    async fetchVariantsAndValues() {
      if (this.selectedProductId) {
        await this.fetchVariants(this.selectedProductId);
        // Fetch existing combination values after variants are loaded
        await this.fetchExistingCombinationValues(this.selectedProductId); // Dispatch action to fetch the existing combination values
      }
    },

    addVariant() {
      const selectedVariant = this.variants.find(variant => variant.id === this.selectedVariantId);
      if (selectedVariant && !this.selectedVariants.includes(selectedVariant)) {
        this.selectedVariants.push(selectedVariant);
        this.selectedVariantValues[selectedVariant.id] = [];
      }
    },

    removeVariant(index) {
      const removedVariant = this.selectedVariants[index];
      this.selectedVariants.splice(index, 1);
      delete this.selectedVariantValues[removedVariant.id];
    },

    openDropdown(variantId) {
      this.dropdownOpen[variantId] = true;
    },

    closeDropdown() {
      setTimeout(() => {
        this.dropdownOpen = {};
      }, 200);
    },

    addTag(variantId, value) {
      if (!this.selectedVariantValues[variantId].includes(value)) {
        this.selectedVariantValues[variantId].push(value);
      }
      this.closeDropdown();
    },

    removeTag(variantId, value) {
      const index = this.selectedVariantValues[variantId].indexOf(value);
      if (index !== -1) {
        this.selectedVariantValues[variantId].splice(index, 1);
      }
    },

    generateCombinations() {
      const valuesArray = Object.values(this.selectedVariantValues);

      if (valuesArray.length > 0) {
        const combinations = this.cartesianProduct(...valuesArray);
        const existingCombinationValues = this.$store.getters['backendVariantCombinations/existingCombinationValues'];

        const duplicates = [];
        const filteredCombinations = combinations.map(combination => {
          const combinationObject = combination.reduce((acc, value, index) => {
            const variantName = this.selectedVariants[index]?.name;
            acc[variantName] = value;
            return acc;
          }, {});

          const combinationExists = existingCombinationValues.some(existingCombination => 
            JSON.stringify(existingCombination) === JSON.stringify(combinationObject)
          );

          if (combinationExists) {
            const existingCombination = Object.entries(combinationObject)
              .map(([key, value]) => `${key}: ${value}`)
              .join(' and ');
            duplicates.push(existingCombination);
            return null;
          }

          return {
            combinationValues: combinationObject,
            price: '',
            image: null,
          };
        }).filter(combination => combination !== null);

        this.combinationList = filteredCombinations;

        // Notify the user about duplicates with successMessage
        if (duplicates.length > 0) {
           this.notificationMessage = `The following combinations already exist and were skipped: ${duplicates.join(', ')}`;

          // Clear the success message after 3 seconds
          setTimeout(() => {
            this.notificationMessage = '';
          }, 5000);
        }
      }
    },

    async updateAllCombinations() {
      try {
        const formData = new FormData();

        this.combinationList.forEach((combination, index) => {
          formData.append(`combinations[${index}][product_id]`, this.selectedProductId);

          // Send the combinationValues as a JSON string
          formData.append(
            `combinations[${index}][combination_values]`,
            JSON.stringify(combination.combinationValues) // Convert to JSON string before sending
          );

          if (combination.price) {
            formData.append(`combinations[${index}][price]`, combination.price);
          }

          // Debugging: Log the combination and image
          console.log(`Combination ${index}:`, combination);

          // Append the image only if it's a valid File object (not null or undefined)
          if (combination.image instanceof File) {
            formData.append(`combinations[${index}][image]`, combination.image);
            console.log(`Image appended for combination ${index}:`, combination.image); // Debugging line
          } else {
            console.log(`No image to append for combination ${index}`); // Debugging line
          }
        });

        // Send the FormData to the backend using your Vuex store
        const response = await this.$store.dispatch('backendVariantCombinations/updateAllCombinations', formData);

        console.log('Combinations updated:', response);
        // Show success message
        this.successMessage = 'All combinations updated successfully!';

        // Hide the success message after 5 seconds
        setTimeout(() => {
          this.successMessage = '';
        }, 5000);

        this.$router.push(`/admin/productvariant/${this.selectedProductId}`);

      } catch (error) {
        console.error('Error updating combinations:', error);
      }
    },

    cartesianProduct(...arrays) {
      return arrays.reduce(
        (a, b) => a.flatMap(d => b.map(e => [...d, e])),
        [[]]
      );
    },

    handleImageUpload(event, index) {
      const file = event.target.files[0];
      if (file) {
        this.combinationList[index].image = file; // Store the image file in the combination
        console.log(`Image uploaded for combination ${index}:`, file); // Debugging line
      } else {
        this.combinationList[index].image = null; // Clear image if none is selected
        console.log(`No image selected for combination ${index}`); // Debugging line
      }
    },

    isHexColor(value) {
      const hexColorRegex = /^#([0-9A-F]{3}){1,2}$/i;
      return hexColorRegex.test(value);
    },
  },
  watch: {
    selectedVariantValues: {
      handler() {
        this.generateCombinations();
      },
      deep: true,
    },
  },
  created() {
    // Check if the product ID exists in the route and set it
    this.selectedProductId = this.$route.params.id;

    // Fetch products and variants
    this.fetchProductById(this.selectedProductId);
    if (this.selectedProductId) {
      this.fetchVariantsAndValues();
    }
  },
};
</script>



<style scoped>
.product-variant {
  padding: 30px;
  background-color: #f4f6f9;
  border-radius: 12px;
  font-family: 'Roboto', sans-serif;
  color: #444;
}

h1 {
  font-size: 26px;
  margin-bottom: 20px;
}

.color-preview {
  width: 20px;
  height: 20px;
  display: inline-block;
  border-radius: 50%;
  margin-right: 8px;
}

/* Tag styles */
.tags-container {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
}

.tag {
  display: flex;
  align-items: center;
  background-color: #007bff;
  color: #fff;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 500;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.tag:hover {
  background-color: #0056b3;
  transform: translateY(-2px);
}

/* Remove button styles */
.tag-remove {
  margin-left: 8px;
  font-size: 18px;
  font-weight: bold;
  color: #fff;
  cursor: pointer;
  line-height: 1;
  transition: color 0.3s ease, transform 0.2s ease;
}

.tag-remove:hover {
  color: #ff4d4d;
  transform: rotate(90deg);
}

/* Remove variant button styles */
.remove-btn {
  margin-top: 10px;
  padding: 8px 16px;
  background-color: #dc3545;
  color: #fff;
  border: none;
  border-radius: 5px;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.remove-btn:hover {
  background-color: #c82333;
  transform: scale(1.05);
}

/* Input styles */
.variant-input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-top: 8px;
  background-color: #fff;
  font-size: 14px;
  color: #444;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.variant-input:focus {
  border-color: #007bff;
  outline: none;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}
.success-message {
  background-color: #28a745; /* Green color */
  color: white;
  padding: 10px;
  border-radius: 5px;
  margin-bottom: 20px;
  font-size: 16px;
  text-align: center;
}
.notification-message {
  background-color: #1F2A44;
  color: #fff;
  padding: 10px;
  border: 1px solid #c3e6cb;
  border-radius: 5px;
  margin-bottom: 20px;
  font-size: 14px;
  text-align: center;
  transition: opacity 0.5s ease;
}
.product-info-container {
  background-color: #1F2A44; /* Dark background color */
  color: white; /* White text color for contrast */
  padding: 10px;
  border-radius: 10px; /* Rounded corners */
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.product-name {
  font-size: 36px;
  font-weight: bold;
  text-align: center;
  margin: 0;
  font-family: 'Roboto', sans-serif;
  text-transform: capitalize;
  letter-spacing: 1px; /* Adds some spacing for a more refined look */
}

.product-description {
  font-size: 18px;
  font-weight: 400;
  text-align: center;
  margin-top: 10px;
  font-family: 'Roboto', sans-serif;
  color: #E0E0E0; /* Lighter gray for description text */
  line-height: 1.5; /* Adds spacing between lines for readability */
  max-width: 800px; /* Ensures the text doesn't stretch too far across wide screens */
}
</style>