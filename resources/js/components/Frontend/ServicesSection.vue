<template>
  <section class="services-section">
    <div class="service" v-for="(service, index) in services" :key="index">
      <div class="icon">
        <i :class="service.icon"></i>
      </div>
      <div class="text">
        <h3>{{ service.title }}</h3>
        <p>{{ service.description }}</p>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "ServicesSection",
  data() {
    return {
      services: [
        {
          title: "Good Quality",
          icon: "fas fa-tools", // FontAwesome icon
          description: "We provide the best quality products with strict quality control.",
        },
        {
          title: "Free Shipping",
          icon: "fas fa-shipping-fast", // FontAwesome icon
          description: "Enjoy free shipping on all orders with no minimum purchase required.",
        },
        {
          title: "Customer Support",
          icon: "fas fa-headset", // FontAwesome icon
          description: "Our customer support team is available 24/7 for any assistance.",
        },
      ],
    };
  },
};
</script>

<style scoped>
.services-section {
  display: flex;
  justify-content: space-between;
  text-align: center;
  padding: 20px;
  background-color: #f8f8f8;
}

.service {
  flex: 1;
  padding: 15px;
  border-radius: 8px;
  background: white;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  margin: 10px;
  transition: transform 0.3s ease-in-out;
}

.service:hover {
  transform: translateY(-5px);
}

.icon {
  font-size: 40px;
  color: #D4AF3A;
  margin-bottom: 10px;
}

.text h3 {
  font-size: 18px;
  margin-bottom: 5px;
}

.text p {
  font-size: 14px;
  color: #555;
}
</style>
