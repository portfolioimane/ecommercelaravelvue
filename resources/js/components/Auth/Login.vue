<template>
  <div class="container mt-5">
    <h1 class="text-center">Login</h1>
    <form @submit.prevent="loginUser">
      <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" id="email" v-model="email" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" id="password" v-model="password" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-golden btn-block mt-3">Login</button>
    </form>
    
    <div v-if="error" class="mt-3 alert alert-danger">{{ error }}</div>
    
    <p class="mt-3 text-center">
      Don't have an account? <router-link to="/register">Register here</router-link>
    </p>
    
    <!-- Forgot Password Link -->
    <p class="mt-3 text-center">
      <router-link to="/forgotpassword">Forgot your password?</router-link>
    </p>
  </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  data() {
    return {
      email: '',
      password: '',
      error: null,
    };
  },
  methods: {
    ...mapActions('auth', ['login']),
    
async loginUser() {
  try {
    console.log('Attempting to login with email:', this.email);

    // Send login credentials
    await this.login({ email: this.email, password: this.password });

    // Get the user from the Vuex state
    const user = this.$store.getters['auth/user'];

    console.log('Login successful, user data:', user);

    // Check user's role and redirect accordingly
if (user.role === 'admin') {
  console.log('User is admin, redirecting to admin/dashboard.');
  this.$router.push({ name: 'AdminDashboard' }); // Adjust route name as needed
} else {
  console.log('User is not admin, redirecting based on query.');
  
  const redirect = this.$route.query.redirect || 'Home';
  
  // Check if the redirect is a valid path or a named route
  if (redirect.startsWith('/') || redirect.includes(':')) {
    // If it's a path (e.g., '/product/1' or '/home')
    this.$router.push({ path: redirect });
  } else {
    // If it's a named route
    this.$router.push({ name: redirect });
  }
}

  } catch (err) {
    console.error('Login failed with error:', err);
    this.error = err.response?.data?.message || 'Login failed. Please check your credentials.';
  }
},

  },
};
</script>

<style scoped>
.container {
  max-width: 500px; /* Limit the width of the form */
}

</style>