<template>
  <MainLayout class="dashboard">
    <template #navigation>
      <Navigation />
    </template>

    <template>
      <h2>Dashboard</h2>
      <p class="header-text">
        Logged in as {{ user?.email ?? 'guest' }}
      </p>
    </template>

    <!-- Main content -->
    <div class="dashboard-content">
      <p class="greeting-text">
        Welcome back,
        <strong>{{ user?.name ?? 'Guest' }}</strong>!
      </p>

      <section class="profile-section user-details" v-if="user">
        <h3>Your Profile</h3>
        <ul>
          <li><strong>Name:</strong> {{ user.name }}</li>
          <li><strong>Email:</strong> {{ user.email }}</li>
          <li>
            <strong>Member since:</strong>
            {{ formattedDate }}
          </li>
        </ul>
      </section>

      <section class="profile-section stripe-invoices" v-if="stripeInvoices && stripeInvoices.length">
        <h3>Invoices</h3>
        <ul>
          <li v-for="invoice in stripeInvoices" :key="invoice.id">
            Invoice #{{ invoice.id }} - Amount: ${{ invoice.amount_due / 100 }} - Status: {{ invoice.status }}
          </li>
        </ul>
      </section>

      <section v-else-if="!stripeInvoices || !stripeInvoices.length && user">
        <p>No recent invoices found.</p>
      </section>

      <section v-else>
        <p>You’re not logged in.</p>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Navigation from '@/Components/Navigation.vue'

const page = usePage()

// 1) debug what props you actually have
console.log('Inertia props:', page.props)

// 2) safely pull out the user (might be undefined)
const user = page.props.auth?.user
const stripeInvoices = computed(() => page.props.stripeInvoices) // Add this line

// 3) format the date if we have one
const formattedDate = computed(() => {
  if (!user?.created_at) return 'N/A'
  return new Date(user.created_at).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
})
</script>
