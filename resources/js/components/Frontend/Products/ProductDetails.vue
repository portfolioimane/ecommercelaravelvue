<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4 text-dark">Product Details</h2>
    <div v-if="product" class="card shadow-lg border-0 rounded-lg">
      <div class="row g-0">
        <div class="col-md-6">
          <img :src="selectedVariant?.image ? `/${selectedVariant.image}` : `/${product.image}`"
               class="card-img-top rounded-start" alt="Product Image" />
        </div>
        <div class="col-md-6">
          <div class="card-body p-4">
            <h3 class="card-title text-dark font-weight-bold">{{ product.name }}</h3>
            <p class="card-text text-muted" style="font-size: 1rem;">{{ product.description }}</p>
            
            <div class="mb-3">
              <p class="font-weight-bold pricegolden">Price: ${{ selectedVariant?.price || product.price }}</p>
            </div>

            <div class="mb-3">
              <p class="font-weight-bold">Category: {{ product.category.name }}</p>
            </div>

            <div class="mb-3">
              <p class="font-weight-bold">Stock: {{ product.stock }}</p>
            </div>

            <!-- Option Selector -->
            <div v-for="(options, optionType) in optionsMap" :key="optionType" class="mb-3">
              <div class="d-flex flex-wrap align-items-center">
                <p class="font-weight-bold mr-2">{{ optionType.charAt(0).toUpperCase() + optionType.slice(1) }}</p>
                <div v-for="option in options" :key="option"
                     class="option-swatch" 
                     :style="getOptionStyle(optionType, option)" 
                     :class="{ 'selected': isSelected(optionType, option) }"
                     @click="selectOption(optionType, option)">
                  <span v-if="optionType === 'color'" class="circle" :style="{ backgroundColor: option }"></span>
                  <span v-else>{{ option }}</span>
                </div>
              </div>
            </div>

            <!-- Quantity Input -->
            <div class="mb-3">
              <label for="quantity" class="form-label font-weight-bold">Quantity</label>
              <input type="number" id="quantity" v-model="quantity" class="form-control" min="1" :max="product.stock" />
            </div>

            <button @click="addToCart" class="btn btn-golden btn-lg btn-block rounded-pill mt-4">Add to Cart</button>
          </div>
        </div>
      </div>
    </div>
    <Review :productId="Number(productId)" />
  </div>
</template>

<script>
import Review from './Review.vue';

export default {
  components: {
    Review
  },
  data() {
    return {
      product: null,
      quantity: 1, // Default quantity
      variants: [], // Store product variants
      selectedOptions: {}, // Store selected options (dynamic based on the options)
      selectedVariant: null, // Store the selected variant object
    };
  },
  created() {
    const id = this.$route.params.id;
    this.$store.dispatch('product/fetchProductById', id)
      .then(() => {
        this.product = this.$store.getters['product/productDetails'];
        this.fetchVariants();
      });
  },
  computed: {
    productId() {
      return this.$route.params.id;  // Capture the product ID from the URL
    },
    optionsMap() {
      // Dynamically generate option types based on available options in the variants
      const map = {};
      this.variants.forEach(variant => {
        Object.keys(variant.combination_values).forEach(optionType => {
          if (!map[optionType]) {
            map[optionType] = [];
          }
          if (!map[optionType].includes(variant.combination_values[optionType])) {
            map[optionType].push(variant.combination_values[optionType]);
          }
        });
      });
      return map;
    }
  },
  methods: {
    fetchVariants() {
      const productId = this.product.id;
      this.$store.dispatch('product/fetchProductVariants', productId)
        .then(() => {
          this.variants = this.$store.getters['product/allProductVariants'];
          if (this.variants.length > 0) {
            const firstVariant = this.variants[0];
            this.selectedOptions = { ...firstVariant.combination_values };
            this.selectedVariant = firstVariant;
          }
        });
    },

    selectOption(optionType, value) {
      this.selectedOptions[optionType] = value;
      this.updateVariant();
    },

    isSelected(optionType, value) {
      return this.selectedOptions[optionType] === value;
    },

    updateVariant() {
      this.selectedVariant = this.variants.find(variant => {
        return Object.keys(this.selectedOptions).every(optionType => {
          return variant.combination_values[optionType] === this.selectedOptions[optionType];
        });
      });
    },

    getOptionStyle(optionType, option) {
      return optionType === 'color' ? { backgroundColor: option } : {};
    },

    addToCart() {
      if (this.quantity < 1 || this.quantity > this.product.stock) {
        alert('Please select a valid quantity');
        return;
      }

      const isAuthenticated = this.$store.getters['auth/isAuthenticated'];
      let sessionId = null;

      if (!isAuthenticated) {
        sessionId = sessionStorage.getItem('session_id');
        if (!sessionId) {
          sessionId = this.generateSessionId();
          sessionStorage.setItem('session_id', sessionId);
        }
      }

      this.$store.dispatch('cart/addProductToCart', {
        product: this.product,
        quantity: this.quantity,
        variant_id: this.selectedVariant?.id || null,
      })
        .then(() => {
          this.$router.push('/cart');
        })
        .catch(error => {
          console.error('Error adding product to cart:', error);
        });
    },

    generateSessionId() {
      return 'sess_' + Math.random().toString(36).substr(2, 9);
    }
  }
};
</script>

<style scoped>
.card-img-top {
  height: 350px;
  object-fit: cover;
  border-top-left-radius: 15px;
  border-bottom-left-radius: 15px;
}

.card-body {
  padding: 2rem;
}

.card-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #333;
}

.card-text {
  font-size: 1rem;
  color: #666;
}

.text-primary {
  color: #0066cc;
}

.card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 15px;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.row.g-0 {
  margin: 0;
}

.form-label {
  font-size: 1rem;
}

.form-control {
  font-size: 1rem;
  padding: 0.75rem;
}

.pricegolden {
  color: #000 !important;
  font-weight: bold;
}

.option-swatch {
  padding: 0.5rem 1rem;
  margin: 0.25rem;
  cursor: pointer;
  border: 1px solid #ddd;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.option-swatch.selected {
  background-color: #ff8c00;
  color: white;
  border-color: #ff8c00;
}

.option-swatch:hover {
  background-color: #ddd;
}

.circle {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 8px;
}
</style>
