<template>
  <div class="admin-dashboard">
    <aside class="sidebar">
      <h2>Admin Dashboard</h2>
      <ul>
        <li>
          <router-link 
            to="/admin/dashboard" 
            class="sidebar-link" 
            :class="{ active: isActive('/admin/dashboard') }">
            <i class="material-icons sidebar-icon">dashboard</i>
            Dashboard
          </router-link>
        </li>
        <li>
          <router-link 
            to="/admin/categories" 
            class="sidebar-link" 
            :class="{ active: isActive('/admin/categories') }">
            <i class="material-icons sidebar-icon">category</i> 
            Categories
          </router-link>
        </li>

        <!-- Manage Products Section -->
        <li>
          <div @click="toggleProductsDropdown" class="dropdown-header">
            <i class="material-icons sidebar-icon">shopping_bag</i>
            Manage Products
            <i class="material-icons dropdown-arrow">{{ isProductsDropdownOpen ? 'arrow_drop_up' : 'arrow_drop_down' }}</i>
          </div>
          <ul v-if="isProductsDropdownOpen" class="dropdown-list">
            <li>
              <router-link 
                to="/admin/products" 
                class="sidebar-link" 
                :class="{ active: isActive('/admin/products') }">View Products</router-link>
            </li>
            <li>
              <router-link 
                to="/admin/products/add" 
                class="sidebar-link" 
                :class="{ active: isActive('/admin/products/add') }">Add Product</router-link>
            </li>
          </ul>
        </li>

        <!-- Manage Orders Section -->
        <li>
          <router-link 
            to="/admin/orders" 
            class="sidebar-link" 
            :class="{ active: isActive('/admin/orders') }">
            <i class="material-icons sidebar-icon">receipt_long</i>
            Manage Orders
          </router-link>
        </li>

        <!-- Manage Customers Section -->
        <li>
          <router-link 
            to="/admin/customers" 
            class="sidebar-link" 
            :class="{ active: isActive('/admin/customers') }">
            <i class="material-icons sidebar-icon">people</i>
            Manage Customers
          </router-link>
        </li>

        <!-- Manage Settings Section -->
        <li>
          <div @click="toggleSettingsDropdown" class="dropdown-header">
            <i class="material-icons sidebar-icon">settings</i>
            Manage Settings
            <i class="material-icons dropdown-arrow">{{ isSettingsDropdownOpen ? 'arrow_drop_up' : 'arrow_drop_down' }}</i>
          </div>
          <ul v-if="isSettingsDropdownOpen" class="dropdown-list">
            <li>
              <router-link 
                to="/admin/paymentsetting" 
                class="sidebar-link" 
                :class="{ active: isActive('/admin/paymentsetting') }">Payments</router-link>
            </li>
          </ul>
        </li>
      </ul>
    </aside>

    <main class="content">
      <!-- View Website and Logout Section -->
      <div class="navbar-section">
        <a :href="websiteUrl" class="navbar-link view-website" target="_blank">
          <i class="material-icons navbar-icon">public</i>
          View Website
        </a>
        <button @click="logout" class="navbar-btn logout-btn">
          <i class="material-icons">exit_to_app</i> Logout
        </button>
      </div>

      <router-view />
    </main>
  </div>
</template>

<script>
export default {
  name: 'AdminLayout',
  data() {
    return {
      isProductsDropdownOpen: false,
      isSettingsDropdownOpen: false, // Added for settings dropdown
    };
  },
  computed: {
    websiteUrl() {
      return window.location.origin;
    }
  },
  methods: {
    toggleProductsDropdown() {
      this.isProductsDropdownOpen = !this.isProductsDropdownOpen;
    },
    toggleSettingsDropdown() {
      this.isSettingsDropdownOpen = !this.isSettingsDropdownOpen; // Toggle for settings dropdown
    },
    isActive(route) {
      return this.$route.path === route;
    },
    logout() {
      // Handle logout logic (adjust according to your authentication system)
      this.$store.dispatch('auth/logout');
      this.$router.push('/login'); // Redirect to login page after logout
    }
  },
  mounted() {
    this.$store.dispatch('auth/checkAuth');
  }
};
</script>

<style scoped>
/* Global reset and font settings */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f4f4f4; /* Light background color for general layout */
}

.admin-dashboard {
  display: flex;
  height: 100vh; /* Full height for the entire dashboard */
  overflow: hidden; /* Prevents scrolling of the parent */
  color: #333;
}

/* Sidebar */
.sidebar {
  position: fixed; /* Sidebar remains fixed in view */
  top: 0;
  left: 0;
  width: 250px;
  height: 100vh; /* Full viewport height */
  background-color: var(--secondary-color); /* Dark blue background */
  color: #f4f4f4; /* Light text for contrast */
  padding: 20px;
  overflow-y: auto; /* Allows scrolling within the sidebar if content overflows */
  border-right: 1px solid #444;
  z-index: 100; /* Ensures the sidebar stays above other elements */
}

/* Content Area */
.content {
  margin-left: 250px; /* Offset the content to accommodate the fixed sidebar */
  flex-grow: 1;
  padding: 20px;
  background-color: #ffffff; /* White background for content */
  color: #333; /* Dark text for readability */
  height: 100vh; /* Match viewport height */
  overflow-y: auto; /* Content area scrolls independently */
}

/* Navbar Section */
.navbar-section {
  background-color: #ffffff; /* White navbar section */
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-radius: 5px;
  padding: 10px 20px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.sidebar h2 {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 30px;
  color: #f4f4f4; /* Gold color for the title to highlight */
}

.sidebar ul {
  padding: 0;
  margin: 0;
  list-style-type: none;
}

.sidebar li {
  margin-bottom: 15px;
}

/* Dropdown Section */
.dropdown-header {
  cursor: pointer;
  padding: 10px;
  font-size: 18px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #f4f4f4;
  transition: background-color 0.3s ease;
}

.dropdown-header:hover {
  background-color: #2A3C5D; /* Slightly lighter blue on hover */
}

.dropdown-list {
  padding-left: 20px;
  margin-top: 10px;
}

/* Sidebar Links */
.sidebar-link {
  color: #f4f4f4;
  text-decoration: none;
  font-size: 16px;
  padding: 12px;
  display: flex;
  align-items: center;
  border-radius: 4px;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.sidebar-link:hover {
  background-color: #2A3C5D; /* Gold color on hover */
}

.sidebar-link.active {
  background-color: #2A3C5D; /* Gold color for active links */
}

.sidebar-icon {
  margin-right: 10px;
}

/* Navbar and Logout Button */
.navbar-link, .navbar-btn {
  font-size: 16px;
  color: var(--secondary-color); /* Dark blue for navbar links */
  font-weight: bold;
  text-decoration: none;
  display: flex;
  align-items: center;
  margin-right: 15px;
  transition: color 0.3s ease;
}

.navbar-link:hover, .navbar-btn:hover {
  color: #D4AF37; /* Gold color on hover */
}

.navbar-icon {
  margin-right: 8px;
}

/* Logout Button */
.navbar-btn {
  color: var(--secondary-color); /* Dark blue text for contrast */
  border: none;
  font-size: 16px;
  cursor: pointer;
  border-radius: 5px;
  padding: 8px 16px;
  display: flex;
  align-items: center;
  transition: background-color 0.3s ease;
  background-color: transparent;
}
</style>
