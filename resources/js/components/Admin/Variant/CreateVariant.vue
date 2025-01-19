<template>
  <div class="variant-container">
    <h2 class="section-title">Create a Variant</h2>
    
    <!-- Variant Creation Form -->
    <form @submit.prevent="createVariant" class="variant-form">
      
      <!-- Variant Name Input -->
      <div class="form-group">
        <label for="variantName">Variant Name</label>
        <input 
          id="variantName" 
          v-model="variant.name" 
          type="text" 
          :placeholder="placeholderText" 
          class="form-input"
          required
        />
      </div>

      <!-- Variant Type Dropdown -->
      <div class="form-group">
        <label for="variantType">Variant Type</label>
        <select 
          id="variantType" 
          v-model="variant.type" 
          class="form-select" 
          required
        >
          <option value="" disabled>Select Variant Type</option>
          <option value="color">Color</option>
          <option value="label">Label</option>
        </select>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary">Add Variant</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      variant: {
        name: '',
        type: '', // Only "color" or "label" will be selected
      },
      placeholderText: "e.g. Size, Color, Material, etc.", // Dynamic placeholder
    };
  },
  methods: {
    async createVariant() {
      try {
        // Dispatch the variant creation action to the backend
        await this.$store.dispatch('backendVariant/createVariant', this.variant);

        // Optionally reset input fields after successful creation
        this.variant.name = '';
        this.variant.type = '';

        // Redirect to admin/variant after success
        this.$router.push('/admin/variant');
      } catch (error) {
        // Handle error if variant creation fails
        console.error("An error occurred while creating the variant", error);
        // Optionally display an error message or alert here
      }
    },
  },
};
</script>

<style scoped>
/* Container Style */
.variant-container {
  padding: 40px;
  max-width: 900px;
  margin: 0 auto;
  background-color: #f9f9f9;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}

/* Title Style */
.section-title {
  font-size: 30px;
  font-weight: 700;
  margin-bottom: 20px;
  color: #333;
  text-align: center;
}

/* Form Style */
.variant-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
}

/* Form Group Style */
.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

/* Label Style */
label {
  font-size: 16px;
  font-weight: 600;
  color: #555;
}

/* Input and Select Style */
.form-input,
.form-select {
  padding: 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 6px;
  transition: border-color 0.3s;
}

/* Input Focus Style */
.form-input:focus,
.form-select:focus {
  border-color: #007bff;
  outline: none;
}

/* Button Style */
.btn {
  padding: 12px 24px;
  font-size: 16px;
  font-weight: 600;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

/* Primary Button Style */
.btn-primary {
  background-color: #D4AF37;
  color: #fff;
}

/* Hover Effect for Primary Button */
.btn-primary:hover {
  background-color: #D4AF37;
}

/* Disabled Button Style */
button:disabled {
  background-color: #d6d6d6;
  cursor: not-allowed;
}
</style>
