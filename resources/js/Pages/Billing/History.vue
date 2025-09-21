<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  invoices: Object, // Laravel pagination object
});

function currency(amount, code) {
  return (amount / 100).toLocaleString('en-US', {
    style: 'currency',
    currency: code.toUpperCase(),
  });
}
</script>

<template>
  <section class="billing-history container">
    <h1>Invoice History</h1>

    <table class="invoice-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Status</th>
          <th>Total</th>
          <th>Paid</th>
          <th></th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="inv in invoices.data" :key="inv.id">
          <td>{{ new Date(inv.stripe_created_at).toLocaleDateString() }}</td>
          <td>{{ inv.status }}</td>
          <td>{{ currency(inv.amount_due, inv.currency) }}</td>
          <td>{{ currency(inv.amount_paid, inv.currency) }}</td>
          <td>
            <a :href="inv.invoice_pdf ?? inv.hosted_invoice_url" target="_blank" class="btn-small">
              Download
            </a>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <nav class="pagination" v-if="invoices.links.length > 3">
      <button
              v-for="link in invoices.links"
              :key="link.url ?? link.label"
              :disabled="!link.url"
              :class="{ active: link.active }"
              @click="router.visit(link.url)"
              v-html="link.label" />
    </nav>
  </section>
</template>

<style lang="scss" scoped>
.billing-history {
  h1 {
    margin-bottom: 1rem;
  }

  .invoice-table {
    width: 100%;
    border-collapse: collapse;
    th,
    td {
      padding: .75rem;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background: #f6f6f6;
    }
  }

  .btn-small {
    display: inline-block;
    padding: .35rem .75rem;
    font-size: .875rem;
    background: #3850ff;
    color: #fff;
    border-radius: 4px;
    text-decoration: none;
  }

  .pagination {
    margin-top: 1.5rem;
    display: flex;
    gap: .25rem;

    button {
      padding: .5rem .75rem;
      border: 1px solid #ccc;
      background: #fff;
      cursor: pointer;

      &.active {
        background: #3850ff;
        color: #fff;
      }
      &:disabled {
        opacity: .4;
        cursor: default;
      }
    }
  }
}
</style>