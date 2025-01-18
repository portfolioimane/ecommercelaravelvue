<template>
  <div class="variant-container">
    <h2 class="section-title">Create Variant Value</h2>
    <form @submit.prevent="createVariantValue" class="variant-form">
      <div class="form-group">
        <label for="variantName">Variant Name</label>
        <input 
          id="variantName" 
          v-model="variant.name" 
          type="text" 
          class="form-input" 
          readonly
        />
      </div>

      <div class="form-group">
        <label for="valueInput">Variant Value</label>
        
        <!-- Conditionally show color picker if variant type is 'color' -->
        <template v-if="variant.type === 'color'">
          <input 
            type="color" 
            v-model="variantValue.value" 
            class="form-input color-picker" 
            required
          />
          <span class="color-preview" :style="{ backgroundColor: variantValue.value }"></span>
        </template>
        
        <!-- Default text input for other variant types -->
        <template v-else>
          <input 
            id="valueInput"
            v-model="variantValue.value" 
            type="text" 
            placeholder="e.g. Red, Small"
            class="form-input" 
            required
          />
        </template>
      </div>

      <button type="submit" class="btn btn-primary">Add Variant Value</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      variant: {
        id: '',
        name: '',
        type: '', // variant type (color or label)
      },
      variantValue: {
        variant_id: '',
        value: '',
      },
    };
  },
  created() {
    this.fetchVariantById();
  },
  methods: {
    async fetchVariantById() {
      const variantId = this.$route.params.id; // Get variant ID from route params
      try {
        await this.$store.dispatch('backendVariant/fetchVariantById', variantId);
        const variant = this.$store.getters['backendVariant/getVariantById'];
        this.variant = variant;
        this.variantValue.variant_id = variant.id; // Set variant_id in the form data
      } catch (error) {
        console.error('Error fetching variant by ID:', error);
      }
    },
    async createVariantValue() {
      try {
        await this.$store.dispatch('backendVariant/createVariantValue', this.variantValue);
        this.variantValue.value = ''; // Reset input after successful creation

        // Redirect to the 'admin/variant' page after successful creation
        this.$router.push('/admin/variant');
      } catch (error) {
        console.error('Error creating variant value:', error);
      }
    },
  },
};
</script>

<style scoped>
.variant-container {
  padding: 30px;
  max-width: 800px;
  margin: 0 auto;
  background-color: #ffffff;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  font-family: 'Arial', sans-serif;
}

.section-title {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 20px;
  color: #333;
}

.variant-form {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

label {
  font-size: 16px;
  font-weight: 600;
  color: #666;
}

.form-input {
  padding: 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
  transition: border-color 0.3s;
}

.form-input:focus {
  border-color: #007bff;
  outline: none;
}

.color-picker {
  width: 100%;
  height: 45px;
  border-radius: 8px;
  border: 1px solid #ccc;
  padding: 8px;
  cursor: pointer;
}

.color-preview {
  width: 50px;
  height: 50px;
  margin-top: 10px;
  border-radius: 50%;
  display: inline-block;
  border: 2px solid #ccc;
}

.btn {
  padding: 14px 25px;
  font-size: 16px;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-primary {
  background-color: #007bff;
  color: #fff;
}

.btn-primary:hover {
  background-color: #0056b3;
}

button:disabled {
  background-color: #d6d6d6;
  cursor: not-allowed;
}
</style>
