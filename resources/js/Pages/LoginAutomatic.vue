<template>
  <MainLayout class="login">
    <template #header>
      <h2>Login (Automated)</h2>
    </template>

    <form @submit.prevent="submit" novalidate>
      <!-- Server errors -->
      <div v-if="Object.keys(form.errors).length" class="validation-errors">
        <div class="alert alert-danger">
          <ul>
            <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
          </ul>
        </div>
      </div>

      <!-- Email field - completely automated -->
      <div class="form-group">
        <label class="form-label" for="email">
          Email Address <span class="required">*</span>
        </label>
        <input class="form-control" :class="{
          'is-invalid': validationErrors.email || form.errors.email,
          'is-valid': !validationErrors.email && !form.errors.email && form.email
        }" id="email" v-model="form.email" type="email" autocomplete="email" />
        <div v-if="validationErrors.email || form.errors.email" class="invalid-feedback">
          {{ validationErrors.email || form.errors.email }}
        </div>
      </div>

      <!-- Password field - completely automated -->
      <div class="form-group">
        <label class="form-label" for="password">
          Password <span class="required">*</span>
        </label>
        <div class="password-input-container">
          <input class="form-control" :class="{
            'is-invalid': validationErrors.password || form.errors.password,
            'is-valid': !validationErrors.password && !form.errors.password && form.password
          }" id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" />
          <button type="button" class="password-toggle" @click="showPassword = !showPassword">
            {{ showPassword ? '🙈' : '👁️' }}
          </button>
        </div>
        <div v-if="validationErrors.password || form.errors.password" class="invalid-feedback">
          {{ validationErrors.password || form.errors.password }}
        </div>
      </div>

      <div class="form-footer">
        <div class="remember-me">
          <label class="form-label">
            <input type="checkbox" v-model="form.remember" class="form-control" />
            <span>Remember Me</span>
          </label>
        </div>

        <button type="submit" :disabled="form.processing || !isValid" class="btn btn-primary">
          {{ form.processing ? 'Logging in…' : 'Log in' }}
        </button>
      </div>
    </form>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import MainLayout from '@/Layouts/MainLayout.vue'
import { useInertiaValidatedForm, validationSchemas } from '@/composables/useValidatedForm.js'

// Password visibility
const showPassword = ref(false)

// This one line sets up ALL validation automatically! 🎉
const { form, validationErrors, isValid, submit } = useInertiaValidatedForm(
  { email: '', password: '', remember: false },  // Initial data
  validationSchemas.login,                       // Validation rules
  route('login')                                 // Submit route
)
</script>
