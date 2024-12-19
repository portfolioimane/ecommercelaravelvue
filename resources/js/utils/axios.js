import axios from 'axios';

const axiosInstance = axios.create({
  baseURL: process.env.MIX_API_URL || 'http://localhost/api', // Adjust baseURL as needed
  timeout: 20000, // Optional: Set a timeout for requests
  withCredentials: false,  // Since we're not using cookies for authentication anymore
});

// Add an interceptor to handle requests and responses
axiosInstance.interceptors.request.use(
  config => {
  
    // Add Authorization header if user-token is available
    const token = localStorage.getItem('user-token');  // Retrieve token from localStorage
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`; // Add Bearer token
    }

    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

export default axiosInstance;
