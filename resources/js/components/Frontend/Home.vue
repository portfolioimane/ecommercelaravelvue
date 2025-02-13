<template>
  <div class="home-page">
    <!-- Home Page Header Section -->
    <HomePageHeader />

    <!-- Services Section -->
    <ServicesSection />

    <!-- Product List Section -->
    <FeaturedProduct />

    <!-- Featured Reviews Section -->
    <FeaturedReviews />
  </div>
</template>

<script>
// Import the components
import HomePageHeader from './HomePageHeader.vue';
import ServicesSection from './ServicesSection.vue';  // Import the new ServicesSection component
import FeaturedProduct from './Products/FeaturedProduct.vue';
import FeaturedReviews from './FeaturedReviews.vue';

export default {
  name: 'Home',
  components: {
    HomePageHeader,
    ServicesSection, // Register the ServicesSection component
    FeaturedProduct,
    FeaturedReviews,
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
