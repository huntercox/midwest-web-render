<template>
  <MainLayout class="home">

    <section class="welcome">
      <div class="container">
        <div class="welcome-content">
          <p class="intro">
            Custom websites from simple to complex, website maintenance, and SEO services.<br /><br />
            Need a website for your business, organization, or personal brand?
          </p>
        </div>
      </div>
    </section>

    <section class="services">
      <div class="container">
        <Services />
      </div>
    </section>

    <section class="contact">
      <div class="container">
        <ContactForm />
      </div>
    </section>

  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import ContactForm from '@/Components/ContactForm.vue';
import Services from '@/Components/Services.vue';
</script>