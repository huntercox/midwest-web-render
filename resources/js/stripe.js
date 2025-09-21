// resources/js/stripe.js

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener('DOMContentLoaded', () => {
  const paymentElementContainer = document.getElementById('payment-element');
  if (!paymentElementContainer) return;

  const stripe = Stripe('pk_test_51QJFQB044qO5dN408CRPzwpq03axC8rJSbXDzhTHzDrp4hRxNMW6ELYHBhSWLwtGuuvxtgpyV84hZ7Wo18B5k1dx00uE360Zha');

  // Setup Payment Element
  const options = {
    mode: 'payment',
    amount: 1099,
    currency: 'usd',
    automatic_payment_methods: {
      enabled: true,
    },
    theme: 'night',
  };

  const elements = stripe.elements(options);
  const paymentElement = elements.create('payment');
  paymentElement.mount('#payment-element');

  // Example: Setup payment form submission event
  const paymentForm = document.getElementById('payment-form');
  paymentForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const response = await fetch('/create-payment-intent', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken // Include CSRF token in the headers
      },
      body: JSON.stringify({ amount: 1099 })
    });
    const data = await response.json();
    const clientSecret = data.clientSecret;

    const { error } = await stripe.confirmPayment({
      elements,
      clientSecret,
      confirmParams: {
        return_url: window.location.origin + '/payment-success',
      },
    });

    if (error) {
      console.error("Payment failed:", error);
    }
  });
});
