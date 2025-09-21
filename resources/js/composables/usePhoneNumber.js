import { ref, computed, watch } from 'vue'
import { formatNumber, parsePhoneNumber, isValidPhoneNumber } from 'libphonenumber-js'

export function usePhoneNumber(initialValue = '') {
  const rawValue = ref(initialValue)
  const displayValue = ref(initialValue)

  // Format phone number as (123) 456-7890
  const formatUSPhone = (value) => {
    if (!value) return ''

    // Remove all non-digits
    const digits = value.replace(/\D/g, '')

    // Limit to 10 digits for US numbers
    const limitedDigits = digits.slice(0, 10)

    // Format based on length
    if (limitedDigits.length === 0) return ''
    if (limitedDigits.length <= 3) return limitedDigits
    if (limitedDigits.length <= 6) {
      return `(${limitedDigits.slice(0, 3)}) ${limitedDigits.slice(3)}`
    }
    return `(${limitedDigits.slice(0, 3)}) ${limitedDigits.slice(3, 6)}-${limitedDigits.slice(6)}`
  }

  // Computed property for formatted value
  const formattedValue = computed(() => {
    return formatUSPhone(rawValue.value)
  })

  // Watch rawValue changes and update displayValue
  watch(rawValue, (newValue) => {
    displayValue.value = formatUSPhone(newValue)
  }, { immediate: true })

  // Computed property for validation
  const isValid = computed(() => {
    if (!rawValue.value) return true // Allow empty values

    const digits = rawValue.value.replace(/\D/g, '')

    // Check if it's a valid US phone number (10 digits)
    if (digits.length !== 10) return false

    try {
      // Use libphonenumber-js for more thorough validation
      return isValidPhoneNumber(digits, 'US')
    } catch {
      return digits.length === 10
    }
  })

  // Get the clean number for form submission
  const cleanValue = computed(() => {
    return rawValue.value.replace(/\D/g, '')
  })

  // Handle input events
  const handleInput = (event) => {
    const inputValue = event.target.value

    // Update the raw value (this will trigger formatting)
    rawValue.value = inputValue

    // The displayValue will be updated automatically by the watcher
    // But we need to ensure the input shows the formatted value
    setTimeout(() => {
      if (event.target === document.activeElement) {
        event.target.value = displayValue.value
      }
    }, 0)
  }

  // Handle keydown for better UX (allow backspace to work naturally)
  const handleKeydown = (event) => {
    // Allow backspace, delete, tab, escape, enter
    if ([8, 9, 27, 13, 46].indexOf(event.keyCode) !== -1 ||
      // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
      (event.keyCode === 65 && event.ctrlKey === true) ||
      (event.keyCode === 67 && event.ctrlKey === true) ||
      (event.keyCode === 86 && event.ctrlKey === true) ||
      (event.keyCode === 88 && event.ctrlKey === true)) {
      return
    }
    // Ensure that it is a number and stop the keypress
    if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105)) {
      event.preventDefault()
    }
  }

  return {
    rawValue,
    displayValue,
    formattedValue,
    isValid,
    cleanValue,
    handleInput,
    handleKeydown,
    formatUSPhone
  }
}
