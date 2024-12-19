<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4 text-dark">Our Premium Products</h2>
    <div class="row">
      <div v-for="product in products" :key="product.id" class="col-lg-4 col-md-6 mb-4">
        <div class="card shadow-lg border-0 rounded-lg h-100">
          <img :src="product.image" class="card-img-top rounded-top" alt="Product" />
          <div class="card-body p-4">
            <h5 class="card-title text-dark font-weight-bold">{{ product.name }}</h5>
            <p class="card-text text-muted" style="font-size: 1rem;">{{ product.description }}</p>
            <p class="text-muted" style="font-size: 0.9rem;">Category: <span class="font-weight-bold">{{ product.category.name }}</span></p>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <p class="font-weight-bold text-primary" style="font-size: 1.25rem;">${{ product.price }}</p>
              <p class="font-weight-bold text-success" style="font-size: 1.1rem;">Stock: {{ product.stock }}</p>
            </div>
            <button @click="viewDetails(product.id)" class="btn btn-warning btn-block rounded-pill mt-3">View Details</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    products() {
      return this.$store.getters['product/allProducts'];
    }
  },
  methods: {
    viewDetails(productId) {
      this.$router.push(`/product/${productId}`);
    }
  },
  created() {
    this.$store.dispatch('product/fetchProducts');
  }
};
</script>

<style scoped>
.card-img-top {
  height: 250px;
  object-fit: cover;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
}
.card-body {
  padding: 1.5rem;
}
.card-title {
  font-size: 1.35rem;
  font-weight: 600;
  color: #333;
}
.card-text {
  font-size: 1rem;
  color: #666;
}
.text-muted {
  font-size: 0.9rem;
}
.text-primary {
  color: #0066cc;
}
.text-success {
  color: #28a745;
}
.btn-warning {
  background-color: #FFD700 !important;
  border-color: #FFD700 !important;
  padding: 12px 20px;
  font-size: 1rem;
  font-weight: 600;
}
.btn-warning:hover {
  background-color: #e6c200 !important;
  border-color: #e6c200 !important;
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}
.card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}
</style>
