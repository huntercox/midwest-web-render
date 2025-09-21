/**
 * Form validation utilities for consistent validation across the application
 */

// Email validation
export function validateEmail(email) {
  const trimmedEmail = email.trim()

  if (!trimmedEmail) {
    return {
      isValid: false,
      message: 'Email address is required'
    }
  }

  // Basic email format validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(trimmedEmail)) {
    return {
      isValid: false,
      message: 'Please enter a valid email address'
    }
  }

  // Check for common email format issues
  if (trimmedEmail.includes('..') || trimmedEmail.startsWith('.') || trimmedEmail.endsWith('.')) {
    return {
      isValid: false,
      message: 'Please enter a valid email address'
    }
  }

  return {
    isValid: true,
    message: ''
  }
}

// Password validation
export function validatePassword(password, options = {}) {
  const {
    minLength = 6,
    requireUppercase = false,
    requireLowercase = false,
    requireNumbers = false,
    requireSpecialChars = false
  } = options

  if (!password) {
    return {
      isValid: false,
      message: 'Password is required'
    }
  }

  if (password.length < minLength) {
    return {
      isValid: false,
      message: `Password must be at least ${minLength} characters long`
    }
  }

  if (requireUppercase && !/[A-Z]/.test(password)) {
    return {
      isValid: false,
      message: 'Password must contain at least one uppercase letter'
    }
  }

  if (requireLowercase && !/[a-z]/.test(password)) {
    return {
      isValid: false,
      message: 'Password must contain at least one lowercase letter'
    }
  }

  if (requireNumbers && !/\d/.test(password)) {
    return {
      isValid: false,
      message: 'Password must contain at least one number'
    }
  }

  if (requireSpecialChars && !/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
    return {
      isValid: false,
      message: 'Password must contain at least one special character'
    }
  }

  return {
    isValid: true,
    message: ''
  }
}

// Password confirmation validation
export function validatePasswordConfirmation(password, confirmation) {
  if (!confirmation) {
    return {
      isValid: false,
      message: 'Please confirm your password'
    }
  }

  if (password !== confirmation) {
    return {
      isValid: false,
      message: 'Passwords do not match'
    }
  }

  return {
    isValid: true,
    message: ''
  }
}

// Name validation
export function validateName(name, fieldName = 'Name') {
  const trimmedName = name.trim()

  if (!trimmedName) {
    return {
      isValid: false,
      message: `${fieldName} is required`
    }
  }

  if (trimmedName.length < 2) {
    return {
      isValid: false,
      message: `${fieldName} must be at least 2 characters long`
    }
  }

  if (trimmedName.length > 100) {
    return {
      isValid: false,
      message: `${fieldName} must be less than 100 characters`
    }
  }

  // Check for valid name characters (letters, spaces, hyphens, apostrophes)
  if (!/^[a-zA-Z\s'-]+$/.test(trimmedName)) {
    return {
      isValid: false,
      message: `${fieldName} can only contain letters, spaces, hyphens, and apostrophes`
    }
  }

  return {
    isValid: true,
    message: ''
  }
}

// Phone validation (US format)
export function validatePhone(phone) {
  const cleanPhone = phone.replace(/\D/g, '')

  if (!cleanPhone) {
    return {
      isValid: false,
      message: 'Phone number is required'
    }
  }

  if (cleanPhone.length !== 10) {
    return {
      isValid: false,
      message: 'Phone number must be 10 digits'
    }
  }

  return {
    isValid: true,
    message: ''
  }
}

// URL validation
export function validateUrl(url) {
  if (!url.trim()) {
    return {
      isValid: false,
      message: 'URL is required'
    }
  }

  try {
    new URL(url)
    return {
      isValid: true,
      message: ''
    }
  } catch {
    return {
      isValid: false,
      message: 'Please enter a valid URL'
    }
  }
}

// Generic required field validation
export function validateRequired(value, fieldName) {
  const trimmedValue = typeof value === 'string' ? value.trim() : value

  if (!trimmedValue && trimmedValue !== 0) {
    return {
      isValid: false,
      message: `${fieldName} is required`
    }
  }

  return {
    isValid: true,
    message: ''
  }
}

// Debounce function for real-time validation
export function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Form validation helper that validates all fields
export function validateForm(fields, validators) {
  const results = {}
  let isFormValid = true

  for (const [fieldName, validator] of Object.entries(validators)) {
    const fieldValue = fields[fieldName]
    const result = validator(fieldValue)
    results[fieldName] = result

    if (!result.isValid) {
      isFormValid = false
    }
  }

  return {
    isValid: isFormValid,
    fields: results
  }
}
