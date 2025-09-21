<template>
  <MainLayout class="login">
    <template #header>
      <h2>Login</h2>
    </template>

    <div class="container">
      <form @submit.prevent="submit" novalidate id="user-login">
        <!-- Global validation alert - only show after submit attempt -->
        <div v-if="!meta.valid && hasSubmitAttempted" class="alert alert-warning mb-4">
          <strong>Please fix the following errors:</strong>
          <ul class="mb-0 mt-2">
            <li v-for="error in Object.values(errors)" :key="error">{{ error }}</li>
          </ul>
        </div>

        <!-- Email field -->
        <div class="form-group">
          <label class="form-label" for="email">
            Email Address <span class="required">*</span>
          </label>
          <input v-model="email" v-bind="emailAttrs" :class="getLoginFieldClasses('email')" id="email" type="email" autocomplete="email" placeholder="Enter your email address" aria-describedby="email-error" />
          <div v-if="hasError('email') && hasSubmitAttempted" class="invalid-feedback" id="email-error" role="alert">
            {{ errors.email }}
          </div>
          <div v-else-if="isValid('email') && hasSubmitAttempted" class="valid-feedback">
            Looks good!
          </div>
        </div>

        <!-- Password field -->
        <div class="form-group">
          <label class="form-label" for="password">
            Password <span class="required">*</span>
          </label>
          <div class="password-input-container">
            <input v-model="password" v-bind="passwordAttrs" :class="getLoginFieldClasses('password')" id="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" placeholder="Enter your password" aria-describedby="password-error" />
            <button type="button" class="password-toggle" @click="togglePasswordVisibility" :aria-label="showPassword ? 'Hide password' : 'Show password'">
              {{ showPassword ? '🙈' : '👁️' }}
            </button>
          </div>
          <div v-if="hasError('password') && hasSubmitAttempted" class="invalid-feedback" id="password-error" role="alert">
            {{ errors.password }}
          </div>
          <div v-else-if="isValid('password') && hasSubmitAttempted" class="valid-feedback">
            Password looks secure!
          </div>
        </div>

        <!-- Remember me -->
        <div class="form-footer">
          <div class="remember-me">
            <label class="form-label">
              <input class="form-control" type="checkbox" v-model="rememberMe" />
              <span>Remember Me</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="!isFormValid || isSubmitting" :class="{ 'loading': isSubmitting }">
            <span v-if="isSubmitting">Signing in...</span>
            <span v-else>Sign In</span>
          </button>
        </div>
      </form>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { useFormValidation } from '../composables/useFormValidation.js'

// Password visibility toggle
const showPassword = ref(false)
const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

// Remember me checkbox
const rememberMe = ref(false)

// Submission state
const isSubmitting = ref(false)
const hasSubmitAttempted = ref(false)

// Initialize automated validation
const {
  errors,
  meta,
  values,
  createField,
  hasError,
  isValid,
  getFieldClasses,
  submit,
  setErrors,
  isFormValid
} = useFormValidation('login', {
  email: '',
  password: ''
}, handleFormSubmission)

// Create validated fields - only validate on submit for better UX
const [email, emailAttrs] = createField('email', {
  validateOnBlur: false,
  validateOnModelUpdate: false
})
const [password, passwordAttrs] = createField('password', {
  validateOnBlur: false,
  validateOnModelUpdate: false
})

/**
 * Get field CSS classes for login form - only show validation states after submit attempt
 */
function getLoginFieldClasses(fieldName, baseClass = 'form-control') {
  return {
    [baseClass]: true,
    'is-invalid': hasError(fieldName) && hasSubmitAttempted.value,
    'is-valid': isValid(fieldName) && hasSubmitAttempted.value
  }
}

/**
 * Handle form submission
 */
async function handleFormSubmission(formData) {
  hasSubmitAttempted.value = true
  isSubmitting.value = true

  try {
    // Submit to Laravel via Inertia
    await router.post(route('login'), {
      ...formData,
      remember: rememberMe.value
    }, {
      onError: (errors) => {
        // Automatically handle server validation errors
        setErrors(errors)
      },
      onFinish: () => {
        isSubmitting.value = false
      }
    })
  } catch (error) {
    console.error('Login error:', error)
    isSubmitting.value = false
  }
}
</script>
