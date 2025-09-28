<template>
  <div id="contact-form" class="contact-form">
    <!-- Success Message -->
    <div v-if="formSubmitted" class="contact-success">
      <div class="success-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
          <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
        </svg>
      </div>
      <h3 class="success-title">Thank you for your message!</h3>
      <p class="success-message">
        We've received your inquiry and will get back to you within 24 hours.
      </p>
      <p class="success-details">
        <small>
          We'll contact you via {{ contactMethod }} to discuss your project needs.
        </small>
      </p>
      <button @click="resetForm" class="btn btn--outline success-reset">
        Send Another Message
      </button>
    </div>

    <!-- Contact Form -->
    <div v-else>
      <div class="contact-form__header">
        <h3>Need help with a website?</h3>
        <p>Contact us today to discuss your project needs.</p>
        <p class="contact-requirement">
          <small>Please provide either an email address or phone number so we can contact you.</small>
        </p>
      </div>

      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label for="name">Name</label>
          <input id="name" v-model="form.name" type="text" class="form-control" required />
        </div>

        <div class="form-group">
          <label for="email">Email <span class="optional">(optional)</span></label>
          <input
                 id="email"
                 v-model="form.email"
                 type="email"
                 class="form-control"
                 :class="{ 'is-invalid': showContactError && !hasContactMethod }" />
        </div>

        <div class="form-group">
          <label for="phone">Phone Number <span class="optional">(optional)</span></label>
          <input
                 id="phone"
                 v-model="phoneNumber"
                 @input="formatPhoneInput"
                 type="tel"
                 class="form-control"
                 :class="{ 'is-invalid': showContactError && !hasContactMethod }"
                 placeholder="(123) 456-7890"
                 maxlength="14"
                 autocomplete="tel"
                 aria-describedby="phone-help" />
        </div>

        <!-- Contact method validation error -->
        <div v-if="showContactError && !hasContactMethod" class="contact-error">
          <div class="invalid-feedback d-block">
            Please provide either an email address or phone number so we can contact you.
          </div>
        </div>

        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" v-model="form.message" class="form-control" rows="4" required></textarea>
        </div>

        <!-- hCaptcha Widget -->
        <div class="form-group">
          <div class="h-captcha-container">
            <div class="h-captcha"
                 :data-sitekey="page.props.hcaptcha_sitekey || 'c7baf8ca-7752-433c-a9a6-03326502cd5c'"
                 data-callback="onHCaptchaVerify"
                 data-expired-callback="onHCaptchaExpired"
                 data-error-callback="onHCaptchaError">
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="form.processing">
          {{ form.processing ? 'Sending...' : 'Send Message' }}
        </button>
      </form>
    </div> <!-- End v-else -->
  </div> <!-- End contact-form -->
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const phoneNumber = ref('');
const showContactError = ref(false);
const formSubmitted = ref(false);
const contactMethod = ref('');

// Check if user has provided at least one contact method
const hasContactMethod = computed(() => {
  const hasEmail = form.email && form.email.trim().length > 0;
  const hasPhone = phoneNumber.value && phoneNumber.value.replace(/\D/g, '').length >= 10;
  return hasEmail || hasPhone;
});

const form = useForm({
  name: '',
  email: '',
  phone: '',
  message: '',
  'h-captcha-response': '',
});

// Watch email field to reset error state
watch(() => form.email, () => {
  if (showContactError.value) {
    showContactError.value = false;
  }
});

const submitForm = () => {
  // Reset error state
  showContactError.value = false;

  // Validate that at least one contact method is provided
  if (!hasContactMethod.value) {
    showContactError.value = true;
    return;
  }

  // Validate hCaptcha completion
  if (!form['h-captcha-response']) {
    alert('Please complete the captcha verification.');
    return;
  }

  // Determine contact method for success message
  const hasEmail = form.email && form.email.trim().length > 0;
  const hasPhone = phoneNumber.value && phoneNumber.value.replace(/\D/g, '').length >= 10;

  if (hasEmail && hasPhone) {
    contactMethod.value = `email (${form.email}) or phone (${phoneNumber.value})`;
  } else if (hasEmail) {
    contactMethod.value = `email (${form.email})`;
  } else if (hasPhone) {
    contactMethod.value = `phone (${phoneNumber.value})`;
  }

  // Clean the phone number for submission
  form.phone = phoneNumber.value.replace(/\D/g, '');

  console.log('Submitting form data:', {
    name: form.name,
    email: form.email,
    phone: form.phone,
    message: form.message,
    'h-captcha-response': form['h-captcha-response'] ? 'present' : 'missing'
  });

  form.post(route('contact.store'), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      console.log('Form submitted successfully');
      // Show success state instead of alert
      formSubmitted.value = true;
    },
    onError: (errors) => {
      // Handle server validation errors
      console.error('Form submission errors:', errors);
      showContactError.value = false;

      // Show a user-friendly error message for specific errors
      if (errors['419'] || errors.message?.includes('419') || errors.message?.includes('expired')) {
        alert('Your session has expired. Please refresh the page and try again.');
        location.reload();
      } else if (errors['h-captcha-response']) {
        alert('Captcha verification failed. Please try again.');
        // Reset hCaptcha
        if (window.hcaptcha) {
          window.hcaptcha.reset();
        }
        form['h-captcha-response'] = '';
      } else if (errors.message || errors.contact) {
        alert('There was an error submitting your form. Please try again.');
      }
    },
    onFinish: () => {
      // This runs regardless of success or error
      console.log('Form submission finished');
    }
  });
};

// Reset form to allow another submission
function resetForm() {
  formSubmitted.value = false;
  form.reset();
  phoneNumber.value = '';
  showContactError.value = false;
  contactMethod.value = '';

  // Reset hCaptcha
  if (window.hcaptcha) {
    window.hcaptcha.reset();
  }
}

// hCaptcha callback functions
function onHCaptchaVerify(token) {
  form['h-captcha-response'] = token;
  console.log('hCaptcha verified:', token);
}

function onHCaptchaExpired() {
  form['h-captcha-response'] = '';
  console.log('hCaptcha expired');
}

function onHCaptchaError(error) {
  form['h-captcha-response'] = '';
  console.error('hCaptcha error:', error);
}

// Make callbacks globally available for hCaptcha
onMounted(() => {
  console.log('Full page props:', page.props);
  console.log('hCaptcha sitekey:', page.props.hcaptcha_sitekey);
  console.log('hCaptcha API loaded:', !!window.hcaptcha);

  window.onHCaptchaVerify = onHCaptchaVerify;
  window.onHCaptchaExpired = onHCaptchaExpired;
  window.onHCaptchaError = onHCaptchaError;
});

onUnmounted(() => {
  // Clean up global callbacks
  delete window.onHCaptchaVerify;
  delete window.onHCaptchaExpired;
  delete window.onHCaptchaError;
});

// Simple phone formatting function
function formatPhoneInput(event) {
  let value = event.target.value.replace(/\D/g, ''); // Remove all non-digits
  let formattedValue = '';

  if (value.length >= 1) {
    formattedValue = value.substring(0, 3);
  }
  if (value.length >= 4) {
    formattedValue = `(${value.substring(0, 3)}) ${value.substring(3, 6)}`;
  }
  if (value.length >= 7) {
    formattedValue = `(${value.substring(0, 3)}) ${value.substring(3, 6)}-${value.substring(6, 10)}`;
  }

  phoneNumber.value = formattedValue;
  event.target.value = formattedValue;

  // Reset error state when user starts typing
  if (showContactError.value) {
    showContactError.value = false;
  }
}
</script>
