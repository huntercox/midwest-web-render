// Enhanced Login component with VeeValidate
<template>
  <MainLayout class="login">
    <template #header>
      <h2>Login</h2>
    </template>

    <Form @submit="onSubmit" :validation-schema="schema">
      <!-- Server-side errors -->
      <div v-if="Object.keys(form.errors).length" class="validation-errors">
        <div class="alert alert-danger">
          <ul>
            <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
          </ul>
        </div>
      </div>

      <!-- Email field with automatic validation -->
      <div class="form-group">
        <label class="form-label" for="email">
          Email Address <span class="required">*</span>
        </label>
        <Field name="email" v-model="form.email" :class="{
          'form-control': true,
          'is-invalid': errors.email || form.errors.email,
          'is-valid': meta.valid && !errors.email && !form.errors.email && form.email
        }" type="email" autocomplete="email" aria-describedby="email-error" />
        <ErrorMessage name="email" class="invalid-feedback" />
        <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
      </div>

      <!-- Password field with automatic validation -->
      <div class="form-group">
        <label class="form-label" for="password">
          Password <span class="required">*</span>
        </label>
        <div class="password-input-container">
          <Field name="password" v-model="form.password" :class="{
            'form-control': true,
            'is-invalid': errors.password || form.errors.password,
            'is-valid': meta.valid && !errors.password && !form.errors.password && form.password
          }" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" aria-describedby="password-error" />
          <button type="button" class="password-toggle" @click="togglePasswordVisibility">
            {{ showPassword ? '🙈' : '👁️' }}
          </button>
        </div>
        <ErrorMessage name="password" class="invalid-feedback" />
        <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
      </div>

      <!-- Remember me -->
      <div class="form-footer">
        <div class="remember-me">
          <label class="form-label">
            <input type="checkbox" v-model="form.remember" class="form-control" />
            <span>Remember Me</span>
          </label>
        </div>

        <button type="submit" :disabled="form.processing || !meta.valid" class="btn btn-primary">
          {{ form.processing ? 'Logging in…' : 'Log in' }}
        </button>
      </div>
    </Form>
  </MainLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Form, Field, ErrorMessage, useForm as useVeeForm } from 'vee-validate'
import * as yup from 'yup'
import MainLayout from '@/Layouts/MainLayout.vue'

// VeeValidate schema - completely automated validation
const schema = yup.object({
  email: yup
    .string()
    .required('Email address is required')
    .email('Please enter a valid email address')
    .matches(
      /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      'Please enter a valid email address'
    ),
  password: yup
    .string()
    .required('Password is required')
    .min(6, 'Password must be at least 6 characters long')
})

// Use VeeValidate's form helpers
const { errors, meta } = useVeeForm({ validationSchema: schema })

// Inertia form for submission
const form = useForm({
  email: '',
  password: '',
  remember: false,
})

// Password visibility
const showPassword = ref(false)

function togglePasswordVisibility() {
  showPassword.value = !showPassword.value
}

function onSubmit(values) {
  // Update form with validated values
  form.email = values.email
  form.password = values.password

  // Submit with Inertia
  form.post(route('login'), {
    onSuccess: () => {
      console.log('✅ Login successful!')
    },
    onError: (errors) => {
      console.error('❌ Login failed:', errors)
    }
  })
}
</script>
