<template>
  <MainLayout class="login">
    <template #header>
      <h2>Login (Automated Validation)</h2>
    </template>

    <form @submit.prevent="submit" novalidate>
      <!-- Global validation alert -->
      <div v-if="!meta.valid && meta.touched" class="alert alert-warning mb-4">
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
        <input v-model="email" v-bind="emailAttrs" :class="getFieldClasses('email')" id="email" type="email" autocomplete="email" placeholder="Enter your email address" aria-describedby="email-error" />
        <div v-if="hasError('email')" class="invalid-feedback" id="email-error" role="alert">
          {{ errors.email }}
        </div>
        <div v-else-if="isValid('email')" class="valid-feedback">
          Looks good!
        </div>
      </div>

      <!-- Password field -->
      <div class="form-group">
        <label class="form-label" for="password">
          Password <span class="required">*</span>
        </label>
        <div class="password-input-container">
          <input v-model="password" v-bind="passwordAttrs" :class="getFieldClasses('password')" id="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" placeholder="Enter your password" aria-describedby="password-error" />
          <button type="button" class="password-toggle" @click="togglePasswordVisibility" :aria-label="showPassword ? 'Hide password' : 'Show password'">
            {{ showPassword ? '🙈' : '👁️' }}
          </button>
        </div>
        <div v-if="hasError('password')" class="invalid-feedback" id="password-error" role="alert">
          {{ errors.password }}
        </div>
        <div v-else-if="isValid('password')" class="valid-feedback">
          Password looks secure!
        </div>
      </div>

      <!-- Remember me -->
      <div class="form-group">
        <div class="form-check">
          <input v-model="rememberMe" class="form-check-input" type="checkbox" id="remember" />
          <label class="form-check-label" for="remember">
            Remember me for 30 days
          </label>
        </div>
      </div>

      <!-- Submit button -->
      <div class="form-footer">
        <button type="submit" class="btn btn-primary btn-block" :disabled="!isFormValid || isSubmitting" :class="{ 'loading': isSubmitting }">
          <span v-if="isSubmitting">Signing in...</span>
          <span v-else>Sign In</span>
        </button>
      </div>

      <!-- Form debug info (remove in production) -->
      <details v-if="isDev" class="mt-4">
        <summary>Debug Info</summary>
        <pre class="text-sm bg-gray-100 p-2 rounded mt-2">{{ {
          values,
          errors,
          meta,
          isFormValid
        } }}</pre>
      </details>
    </form>
  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { useFormValidation } from '@/composables/useFormValidation'

// Password visibility toggle
const showPassword = ref(false)
const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

// Remember me checkbox
const rememberMe = ref(false)

// Submission state
const isSubmitting = ref(false)

// Development mode check
const isDev = computed(() => import.meta.env.DEV)

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

// Create validated fields
const [email, emailAttrs] = createField('email')
const [password, passwordAttrs] = createField('password')

/**
 * Handle form submission
 */
async function handleFormSubmission(formData) {
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

<style lang="scss" scoped>
.login {
  max-width: 400px;
  margin: 0 auto;
  padding: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #374151;
}

.required {
  color: #ef4444;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: all 0.2s ease;

  &:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  &.is-valid {
    border-color: #10b981;
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23198754' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 5l-6 6-3-3'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
  }

  &.is-invalid {
    border-color: #ef4444;
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23dc2626' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 1l7 14H1L8 1z'/%3E%3Cpath fill='none' stroke='%23dc2626' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 6v4'/%3E%3Ccircle cx='8' cy='12' r='1' fill='%23dc2626'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
  }
}

.password-input-container {
  position: relative;
  display: flex;
  align-items: center;

  .password-toggle {
    position: absolute;
    right: 0.75rem;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    z-index: 10;

    &:hover {
      opacity: 0.7;
    }
  }

  .form-control.is-valid,
  .form-control.is-invalid {
    padding-right: 4rem; // Space for both toggle and validation icon
  }
}

.form-check {
  display: flex;
  align-items: center;

  .form-check-input {
    margin-right: 0.5rem;
  }
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;

  &.btn-primary {
    background-color: #3b82f6;
    color: white;

    &:hover:not(:disabled) {
      background-color: #2563eb;
      transform: translateY(-1px);
    }

    &:disabled {
      background-color: #9ca3af;
      cursor: not-allowed;
      transform: none;
    }

    &.loading {
      background-color: #6b7280;
    }
  }

  &.btn-block {
    width: 100%;
  }
}

.valid-feedback {
  color: #10b981;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.invalid-feedback {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.alert {
  padding: 1rem;
  border-radius: 0.5rem;
  border: 1px solid;

  &.alert-warning {
    background-color: #fef3c7;
    border-color: #f59e0b;
    color: #92400e;
  }

  ul {
    list-style-type: disc;
    margin-left: 1.25rem;
  }
}
</style>
