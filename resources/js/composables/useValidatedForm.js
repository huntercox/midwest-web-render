import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import * as yup from 'yup'

/**
 * Laravel + Inertia + VeeValidate composable for automatic form validation
 * This eliminates most manual validation work
 */
export function useInertiaValidatedForm(initialData, validationSchema, submitRoute) {
  // Inertia form
  const form = useForm(initialData)

  // Validation state
  const validationErrors = computed(() => {
    const errors = {}

    for (const field in initialData) {
      try {
        validationSchema.validateSyncAt(field, form.data())
        errors[field] = null
      } catch (error) {
        errors[field] = error.message
      }
    }

    return errors
  })

  // Check if form is valid
  const isValid = computed(() => {
    try {
      validationSchema.validateSync(form.data())
      return true
    } catch {
      return false
    }
  })

  // Validate single field
  const validateField = (fieldName) => {
    try {
      validationSchema.validateSyncAt(fieldName, form.data())
      return { isValid: true, message: '' }
    } catch (error) {
      return { isValid: false, message: error.message }
    }
  }

  // Submit with validation
  const submit = (options = {}) => {
    if (!isValid.value) {
      console.log('Form validation failed')
      return
    }

    form.post(submitRoute, {
      onSuccess: () => {
        console.log('✅ Form submitted successfully')
      },
      onError: (errors) => {
        console.error('❌ Form submission failed:', errors)
      },
      ...options
    })
  }

  return {
    form,
    validationErrors,
    isValid,
    validateField,
    submit
  }
}

// Pre-built validation schemas
export const validationSchemas = {
  login: yup.object({
    email: yup
      .string()
      .required('Email address is required')
      .email('Please enter a valid email address'),
    password: yup
      .string()
      .required('Password is required')
      .min(6, 'Password must be at least 6 characters long')
  }),

  register: yup.object({
    name: yup
      .string()
      .required('Name is required')
      .min(2, 'Name must be at least 2 characters')
      .max(50, 'Name must be less than 50 characters'),
    email: yup
      .string()
      .required('Email address is required')
      .email('Please enter a valid email address'),
    password: yup
      .string()
      .required('Password is required')
      .min(8, 'Password must be at least 8 characters long')
      .matches(/[A-Z]/, 'Password must contain at least one uppercase letter')
      .matches(/[0-9]/, 'Password must contain at least one number'),
    password_confirmation: yup
      .string()
      .required('Please confirm your password')
      .oneOf([yup.ref('password')], 'Passwords do not match')
  }),

  contact: yup.object({
    name: yup
      .string()
      .required('Name is required')
      .min(2, 'Name must be at least 2 characters'),
    email: yup
      .string()
      .required('Email address is required')
      .email('Please enter a valid email address'),
    message: yup
      .string()
      .required('Message is required')
      .min(10, 'Message must be at least 10 characters')
      .max(1000, 'Message must be less than 1000 characters')
  })
}
