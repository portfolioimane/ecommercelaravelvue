<template>
  <div class="home-page">
    <!-- Home Page Header Section -->
    <HomePageHeader />

    <!-- Product List Section -->
    <ProductList />
  </div>
</template>

<script>
// Import the components
import HomePageHeader from './HomePageHeader.vue';
import ProductList from './Products/ProductList.vue';

export default {
  name: 'Home',
  components: {
    HomePageHeader,
    ProductList,
  },
};
</script>

<style scoped>
.home-page {
  display: flex;
  flex-direction: column;
  padding: 0;
}
</style>
