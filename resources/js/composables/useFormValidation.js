import { useForm } from 'vee-validate'
import * as yup from 'yup'

/**
 * Pre-configured validation schemas for common forms
 */
export const validationSchemas = {
  login: yup.object({
    email: yup
      .string()
      .required('Email is required')
      .email('Please enter a valid email address'),
    password: yup
      .string()
      .required('Password is required')
      .min(8, 'Password must be at least 8 characters')
  }),

  register: yup.object({
    name: yup
      .string()
      .required('Name is required')
      .min(2, 'Name must be at least 2 characters'),
    email: yup
      .string()
      .required('Email is required')
      .email('Please enter a valid email address'),
    password: yup
      .string()
      .required('Password is required')
      .min(8, 'Password must be at least 8 characters')
      .matches(/[A-Z]/, 'Password must contain at least one uppercase letter')
      .matches(/[a-z]/, 'Password must contain at least one lowercase letter')
      .matches(/\d/, 'Password must contain at least one number'),
    password_confirmation: yup
      .string()
      .required('Please confirm your password')
      .oneOf([yup.ref('password')], 'Passwords must match')
  }),

  forgotPassword: yup.object({
    email: yup
      .string()
      .required('Email is required')
      .email('Please enter a valid email address')
  }),

  contact: yup.object({
    name: yup
      .string()
      .required('Name is required')
      .min(2, 'Name must be at least 2 characters'),
    email: yup
      .string()
      .required('Email is required')
      .email('Please enter a valid email address'),
    subject: yup
      .string()
      .required('Subject is required')
      .min(5, 'Subject must be at least 5 characters'),
    message: yup
      .string()
      .required('Message is required')
      .min(10, 'Message must be at least 10 characters')
  })
}

/**
 * Automated form validation composable
 *
 * @param {string} schemaName - The name of the validation schema to use
 * @param {Object} initialValues - Initial form values
 * @param {Function} onSubmit - Function to call when form is submitted
 * @returns {Object} - VeeValidate form utilities
 */
export function useFormValidation(schemaName, initialValues = {}, onSubmit = null) {
  const schema = validationSchemas[schemaName]

  if (!schema) {
    throw new Error(`Validation schema "${schemaName}" not found`)
  }

  const form = useForm({
    validationSchema: schema,
    initialValues
  })

  const {
    errors,
    meta,
    values,
    handleSubmit,
    defineField,
    setFieldError,
    setErrors
  } = form

  /**
   * Create a field with automatic validation
   * @param {string} name - Field name
   * @param {Object} options - Field options
   * @returns {Array} - [value, attrs] for the field
   */
  const createField = (name, options = {}) => {
    return defineField(name, {
      validateOnModelUpdate: true,
      validateOnBlur: true,
      ...options
    })
  }

  /**
   * Handle form submission with automatic validation
   */
  const submit = handleSubmit(async (values) => {
    if (onSubmit) {
      try {
        await onSubmit(values)
      } catch (error) {
        // Handle server validation errors
        if (error.response?.data?.errors) {
          setErrors(error.response.data.errors)
        }
      }
    }
  })

  /**
   * Check if a field has errors
   */
  const hasError = (fieldName) => {
    return !!errors.value[fieldName]
  }

  /**
   * Check if a field is valid
   */
  const isValid = (fieldName) => {
    return !errors.value[fieldName] && meta.value.touched[fieldName]
  }

  /**
   * Get field CSS classes for styling
   */
  const getFieldClasses = (fieldName, baseClass = 'form-control') => {
    return {
      [baseClass]: true,
      'is-invalid': hasError(fieldName),
      'is-valid': isValid(fieldName)
    }
  }

  return {
    // Form state
    errors,
    meta,
    values,

    // Field creation
    createField,

    // Validation helpers
    hasError,
    isValid,
    getFieldClasses,

    // Form submission
    submit,

    // Error handling
    setFieldError,
    setErrors,

    // Form validity
    isFormValid: meta.value.valid
  }
}

/**
 * Field validation states for consistent styling
 */
export const fieldStates = {
  default: '',
  valid: 'is-valid',
  invalid: 'is-invalid'
}

/**
 * Common validation rules that can be reused
 */
export const commonRules = {
  required: (message = 'This field is required') =>
    yup.string().required(message),

  email: (message = 'Please enter a valid email address') =>
    yup.string().email(message),

  minLength: (length, message = `Must be at least ${length} characters`) =>
    yup.string().min(length, message),

  maxLength: (length, message = `Must be no more than ${length} characters`) =>
    yup.string().max(length, message),

  phone: (message = 'Please enter a valid phone number') =>
    yup.string().matches(/^\+?[\d\s\-\(\)]+$/, message),

  url: (message = 'Please enter a valid URL') =>
    yup.string().url(message),

  strongPassword: (message = 'Password must contain uppercase, lowercase, and number') =>
    yup.string()
      .min(8, 'Password must be at least 8 characters')
      .matches(/[A-Z]/, 'Password must contain at least one uppercase letter')
      .matches(/[a-z]/, 'Password must contain at least one lowercase letter')
      .matches(/\d/, 'Password must contain at least one number')
}
