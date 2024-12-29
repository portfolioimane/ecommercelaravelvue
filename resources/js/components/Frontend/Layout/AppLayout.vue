<template>
  <div>
    <Header />
    <main>
      <router-view />
    </main>
  </div>
</template>

<script>
import Header from '../Header.vue';

export default {
  name: "AppLayout",
  components: {
    Header,
  },
};
</script>

<style scoped>
main {
  padding: 20px;
  
}
</style>
