<template>
  <div>
    <Header />


    <main>
      <router-view />
    </main>

        <Footer />
  </div>
</template>

<script>
import Header from '../Header.vue';
import Footer from './Footer.vue';

export default {
  name: "AppLayout",
  components: {
    Header,
    Footer,
  },
};
</script>

<style scoped>
main {
  
}
</style>
