<template>
  <div v-if="header" class="hero-section">
    <!-- Background Image with Overlay -->
    <div
      class="hero-bg"
      :style="{ backgroundImage: `url(${header.image})` }"
    >
      <!-- Overlay on Background Image -->
      <div class="hero-overlay"></div>
      
      <!-- Content -->
      <div class="hero-content">
        <h1 class="hero-title">{{ header.title }}</h1>
        <p class="hero-subtitle">{{ header.subtitle }}</p>
        <button class="hero-btn" @click="handleButtonClick">
          {{ header.buttonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  data() {
    return {
      // Add any other local data if needed
    };
  },
  computed: {
    ...mapGetters('backendHomePageHeader', ['getHeader', 'getError']),
    header() {
      return this.getHeader;
    },
  },
  mounted() {
    this.fetchHeaderFront();
  },
  methods: {
    async fetchHeaderFront() {
      try {
        await this.$store.dispatch('backendHomePageHeader/fetchHeaderFront');
      } catch (error) {
        console.error('Error fetching homepage header:', error);
      }
    },
    handleButtonClick() {
      // Handle button click (e.g., navigate to the shop or another page)
      this.$router.push('/shop');
    },
  },
};
</script>

<style scoped>
.hero-section {
  width: 100%;
  height: 100vh;
  position: relative;
}

.hero-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  z-index: 0; /* Make sure the overlay and content are above the background */
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* 50% black overlay */
  z-index: 1; /* Overlay above the background */
}

.hero-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  color: white;
  z-index: 2; /* Content above overlay */
}

.hero-title {
  font-size: 3rem;
  font-weight: bold;
}

.hero-subtitle {
  font-size: 1.5rem;
  margin-top: 1rem;
}

.hero-btn {
  margin-top: 2rem;
  padding: 1rem 2rem;
  background-color: #D4AF37; /* Gold color for button */
  color: white;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.hero-btn:hover {
  background-color: #b8972e; /* Darker gold on hover */
}
</style>
