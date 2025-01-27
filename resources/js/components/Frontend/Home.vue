<template>
  <div class="home-page">
    <!-- Home Page Header Section -->
    <HomePageHeader />

    <!-- Product List Section -->
    <ProductList />

        <!-- Featured Reviews Section -->
    <FeaturedReviews />
  </div>
</template>

<script>
// Import the components
import HomePageHeader from './HomePageHeader.vue';
import ProductList from './Products/ProductList.vue';
import FeaturedReviews from './FeaturedReviews.vue';  // Import the new FeaturedReviews component

export default {
  name: 'Home',
  components: {
    HomePageHeader,
    ProductList,
    FeaturedReviews,  // Register the FeaturedReviews component
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
