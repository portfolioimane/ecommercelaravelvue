<template>
  <div class="container mt-5 checkout-container">
    <h2 class="text-center mb-4">Checkout</h2>

    <div class="row">
      <div class="col-md-6">
        <h4 class="mb-4">Customer Information</h4>
        <form @submit.prevent="submitOrder">
          <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
          <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>

          <!-- Customer information form fields -->
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" v-model="form.name" class="form-control" required />
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" v-model="form.email" class="form-control" required />
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" id="phone" v-model="form.phone" class="form-control" required />
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" id="address" v-model="form.address" class="form-control" required />
          </div>

          <!-- Payment method selection -->
          <h4 class="mb-4">Select Payment Method</h4>
          <div class="form-check">
            <input
              type="radio"
              id="cod"
              v-model="form.payment_method"
              value="cash_on_delivery"
              class="form-check-input"
              required
            />
            <label class="form-check-label" for="cod">Cash on Delivery</label>
          </div>
          <div class="form-check" v-if="isStripeEnabled">
            <input type="radio" id="stripe" v-model="form.payment_method" value="stripe" class="form-check-input" />
            <label class="form-check-label" for="stripe">
              <i class="fas fa-credit-card"></i> Credit Card
            </label>
          </div>
          <div v-if="form.payment_method === 'stripe'" class="mb-3">
            <div id="cardElement" class="form-control"></div>
          </div>
          <div class="form-check" v-if="isPaypalEnabled">
            <input
              type="radio"
              id="paypal"
              v-model="form.payment_method"
              value="paypal"
              class="form-check-input"
            />
            <label class="form-check-label" for="paypal">PayPal</label>
          </div>

          <!-- PayPal Button -->
<div v-show="form.payment_method === 'paypal'" class="paypal-button-container">
  <div id="paypal-button-container" v-if="isFormValid"></div>
  <div v-else class="alert alert-warning">Please complete all fields before proceeding with PayPal.</div>
</div>



          <!-- Submit button with loading state, but only show it if PayPal is NOT selected -->
          <button
            v-if="form.payment_method !== 'paypal'"
            type="submit"
            class="btn btn-golden mt-3 w-100"
            :disabled="!isFormValid || loading"
          >
            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Place Order
          </button>
        </form>
      </div>

<div class="col-md-6">
  <h4 class="mb-4 text-center">Order Summary</h4>
  <ul class="list-group">
    <li 
      v-for="item in cartItems" 
      :key="item.id" 
      class="list-group-item d-flex flex-column align-items-start"
    >
<div class="d-flex justify-content-between align-items-center w-100">
  <span>{{ item.product.name }} (x{{ item.quantity }})</span>
  <span>${{ item.variant?.price || item.product.price }}</span>
  <span class="text-golden">
    ${{ (item.quantity * (item.variant?.price || item.product.price)).toFixed(2) }}
  </span>
</div>

      
      <!-- Combination Values -->
      <div v-if="item.variant?.combination_values" class="combination-values mt-2">
        <div 
          v-for="(value, key) in item.variant.combination_values" 
          :key="key" 
          class="combination-item"
        >
          <span class="key-label">{{ key }}:</span>
          <span v-if="key === 'color'">
            <span 
              class="color-circle" 
              :style="{ backgroundColor: value }"
            ></span>
          </span>
          <span v-else>{{ value }}</span>
        </div>
      </div>
      <span v-else class="text-muted mt-2"></span>
    </li>
  </ul>
  <h5 class="text-end mt-4">Total: <span class="text-golden">${{ totalCartValue.toFixed(2) }}</span></h5>
</div>

    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { loadStripe } from "@stripe/stripe-js";

export default {
  data() {
    return {
      form: {
        name: '',
        email: '',
        phone: '',
        address: '',
        payment_method: 'cash_on_delivery',
      },
      stripe: null,
      elements: null,
      cardElement: null,
      errorMessage: null,
      successMessage: null,
      loading: false,
      paypalLoaded: false,
    };
  },
  computed: {
    ...mapGetters("keys", ["getStripePublicKey", "getPaypalPublicKey"]), // Namespaced getter
    ...mapGetters("keys", ["isStripeEnabled", "isPaypalEnabled"]), // Namespaced getter

    cartItems() {
      return this.$store.getters['cart/cartItems'];
    },
    totalCartValue() {
      return this.$store.getters['cart/totalCartValue'];
    },
    isFormValid() {
      return this.form.name && this.form.email && this.form.phone && this.form.address && this.cartItems.length > 0;
    },
    user() {
      return this.$store.getters['auth/user']; // Get logged-in user data
    },
  },
  methods: {
  ...mapActions("keys", ["fetchStripePublicKey", "fetchPaypalPublicKey"]), // Namespaced action
  getOrderData() {
    return {
      name: this.form.name || this.user.name,
      email: this.form.email || this.user.email,
      phone: this.form.phone || this.user.phone,
      address: this.form.address || this.user.address,
      payment_method: this.form.payment_method,
      items: this.cartItems,
      total: this.totalCartValue,
    };
  },
    async submitOrder() {
      if (!this.isFormValid) {
        this.errorMessage = "Please complete all fields and ensure your cart is not empty.";
        return;
      }

      // Set loading state to true
      this.loading = true;
    const orderData = this.getOrderData(); // Use the new method

      try {
        if (this.form.payment_method === 'stripe') {
          const { clientSecret } = await this.$store.dispatch('orders/createStripePayment', this.totalCartValue);

          if (!clientSecret) throw new Error("Payment initialization failed.");

          const { error, paymentIntent } = await this.stripe.confirmCardPayment(clientSecret, {
            payment_method: {
              card: this.cardElement,
              billing_details: {
                name: this.form.name,
                email: this.form.email,
              },
            },
          });

          if (error) throw error;

          if (paymentIntent.status === 'succeeded') {
             await this.submitOrderAfterPayment(orderData); // Call the reusable order submission method
          } else {
            throw new Error("Payment not successful.");
          }
        } else{
          await this.submitOrderAfterPayment(orderData); // Call the reusable order submission method

        }

  
      } catch (error) {
        this.errorMessage = error.message || "Failed to place order.";
      } finally {
        this.loading = false;
      }
    },

    async submitOrderAfterPayment(orderData) {
    try {
      const response = await this.$store.dispatch('orders/submitOrder', orderData);

      const orderId = response.id;
      this.$store.commit('cart/setCart', []);
      this.form = {
        name: '',
        email: '',
        phone: '',
        address: '',
        payment_method: 'cash_on_delivery',
      };
      this.$router.push(`/customerdashboard/order/${orderId}`);
    } catch (error) {
      console.error('Error submitting order:', error);
      alert('Failed to place the order. Please try again.');
    }
  },

        fetchCart() {
      this.$store.dispatch('cart/fetchCart');
    },
    fillUserData() {
      if (this.user) {
        this.form.name = this.user.name || '';
        this.form.email = this.user.email || '';
        this.form.phone = this.user.phone || '';
        this.form.address = this.user.address || '';
      }
    },
    async initializeStripe() {
      // If a Stripe element already exists, destroy it before reinitializing
      if (this.cardElement) {
        this.cardElement.unmount();
        this.cardElement = null;
      }
      await this.fetchStripePublicKey();
      const stripePublicKey = this.getStripePublicKey;

      if (!stripePublicKey) {
        console.error("Stripe public key is not available");
        return;
      }

      const stripePromise = loadStripe(stripePublicKey);
      this.stripe = await stripePromise;
      this.elements = this.stripe.elements();
      this.cardElement = this.elements.create('card');
      this.cardElement.mount('#cardElement');
    },


loadPayPalScript() {
  if (!this.paypalLoaded) {
    // Fetch PayPal public key from Vuex store asynchronously
    this.fetchPaypalPublicKey().then(() => {
      const clientId = this.getPaypalPublicKey;

      if (!clientId) {
        console.error('PayPal public key is not available');
        return;
      }

      const script = document.createElement('script');
      script.src = `https://www.paypal.com/sdk/js?client-id=${clientId}&currency=USD`;
      script.async = true;
      script.onload = () => {
        this.paypalLoaded = true;
        this.renderPayPalButton();
      };
      document.body.appendChild(script);
    }).catch(error => {
      console.error('Error fetching PayPal public key:', error);
    });
  } else {
    this.renderPayPalButton();
  }
},



renderPayPalButton() {

  
  // If validation passes, render the PayPal button
  const interval = setInterval(() => {
    const container = document.getElementById('paypal-button-container');
    if (container) {
      clearInterval(interval);
      container.innerHTML = ''; // Clear previous button rendering

      window.paypal.Buttons({
        createOrder: (data, actions) => {
          return actions.order.create({
            purchase_units: [
              {
                amount: {
                  value: this.totalCartValue.toFixed(2),
                },
              },
            ],
          });
        },
        onApprove: (data, actions) => {
          return actions.order.capture().then(async (details) => {
            const paypalOrderId = data.orderID;
            try {
              const response = await this.$store.dispatch('orders/confirmPayPalPayment', paypalOrderId);
              if (response.status === 'COMPLETED') {
                const orderData = this.getOrderData();
                await this.submitOrderAfterPayment(orderData);
              } else {
                this.errorMessage = 'Payment not completed.';
              }
            } catch (error) {
              this.errorMessage = 'Failed to confirm PayPal payment.';
            }
          });
        },
        onError: (err) => {
          this.errorMessage = 'An error occurred with PayPal.';
        },
      }).render('#paypal-button-container');
    }
  }, 100);
},



  },
watch: {
  'form.payment_method': function (newValue) {
    if (newValue === 'stripe') {
      this.initializeStripe();
    } else if (newValue === 'paypal') {
      this.loadPayPalScript();
    }
  },
   isFormValid(newValue) {
      // Watch for changes in form validity and show/hide PayPal button
      if (newValue && this.form.payment_method === 'paypal') {
        this.loadPayPalScript();
      }
    },
},

  mounted() {
    if (this.form.payment_method === 'stripe') {
      this.initializeStripe();
    }
    if (this.form.payment_method === 'paypal') {
        this.loadPayPalScript();
    }
    this.fetchCart();
        this.fetchStripePublicKey();
        this.fetchPaypalPublicKey();

    this.$store.dispatch('auth/checkAuth').then(() => {
      this.fillUserData();
    });
  },
};
</script>


<style scoped>
.checkout-container {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2, h4 {
  color: #333;
}

.text-golden {
  color: #d4af37; /* Golden color */
}

.btn-primary {
  background-color: #d4af37 !important;
  border: none !important;
  color: #fff !important;
}

.btn-primary:hover {
  background-color: #b6932f !important;
}

.list-group-item {
  border: none;
  background-color: #fff;
  padding: 12px 20px;
  font-size: 1rem;
}

.list-group-item + .list-group-item {
  margin-top: 8px;
}

.list-group-item .text-golden {
  font-weight: bold;
}

.form-check-label {
  font-weight: normal;
  font-size: 1rem;
}

.form-check-input:checked {
  background-color: #E6B800;
  border-color: #E6B800;
}
</style>

<style scoped>
.checkout-container {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2, h4 {
  color: #333;
}

.text-golden {
  color: #d4af37; /* Golden color */
}

.btn-primary {
  background-color: #d4af37 !important;
  border: none !important;
  color: #fff !important;
}

.btn-primary:hover {
  background-color: #b6932f !important;
}

.list-group-item {
  border: none;
  background-color: #fff;
  padding: 12px 20px;
  font-size: 1rem;
}

.list-group-item + .list-group-item {
  margin-top: 8px;
}

.list-group-item .text-golden {
  font-weight: bold;
}

.form-check-label {
  font-weight: normal;
  font-size: 1rem;
}

.form-check-input:checked {
  background-color: #E6B800;
  border-color: #E6B800;
}

.combination-values {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 10px;
}

.combination-item {
  display: flex;
  align-items: center;
  font-size: 0.9rem;
}

.key-label {
  font-weight: bold;
  margin-right: 5px;
}

.color-circle {
  display: inline-block;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  border: 1px solid #ccc;
  margin-left: 5px;
}


</style>
