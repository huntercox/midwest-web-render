<template>
  <div>
    <header class="site-header">
      <div class="container">
        <a href="/" class="logo">
          <p role="logo">Midwest Web</p>
        </a>

        <Navigation />
      </div>
    </header>

    <main>
      <!-- Page Heading -->
      <div v-if="$slots.header" class="banner">
        <div class="container">
          <slot name="header" />
        </div>
      </div>

      <!-- Page Content -->
      <div class="page-content">
        <slot />
      </div>
    </main>

    <footer class="site-footer">
      <div class="container">
        <p>
          Copyright<span> © </span>2025 - Midwest Web LLC
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import Navigation from '@/Components/Navigation.vue'
</script>