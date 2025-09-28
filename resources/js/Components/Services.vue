<template>
  <div class="services__header">
    <h2 class="services__title">Services</h2>
  </div>
  <ul class="services__list">
    <li class="services__item" v-for="(service, index) in services" :key="index">
      <a href="#" class="services__link" @click.prevent="openModal(service)">
        <span class="services__name">{{ service.name }}</span>
        <span class="services__price">{{ service.price }}</span>
      </a>
    </li>
  </ul>

  <!-- Modal -->
  <div v-if="showModal" class="modal-overlay" @click="closeModal">
    <div class="modal" @click.stop>
      <div class="modal__header">
        <h3 class="modal__title">{{ selectedService?.name }}</h3>
        <button class="modal__close" @click="closeModal">&times;</button>
      </div>
      <div class="modal__body">
        <div class="modal__price">{{ selectedService?.price }}</div>
        <p class="modal__description">{{ selectedService?.description }}</p>
        <div class="modal__features">
          <h4>What's Included:</h4>
          <ul>
            <li v-for="feature in selectedService?.features" :key="feature">{{ feature }}</li>
          </ul>
        </div>
        <div class="modal__timeline" v-if="selectedService?.timeline">
          <h4>Timeline:</h4>
          <p>{{ selectedService.timeline }}</p>
        </div>
      </div>
      <div class="modal__footer">
        <button
                class="btn btn--green"
                @click="contactUs"
                :disabled="isContactingUs">
          {{ isContactingUs ? 'Taking you to contact form...' : 'Get Started' }}
        </button>
        <button class="btn btn--outline" @click="closeModal">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const showModal = ref(false)
const selectedService = ref(null)
const isContactingUs = ref(false)

const services = ref([
  {
    name: 'Basic Website Setup',
    description: 'A professional website setup perfect for small businesses and startups looking to establish their online presence.',
    features: [
      'Responsive design for all devices',
      'Content management system',
      'Basic SEO optimization',
      'Contact form integration',
      'Social media integration',
      '30 days of support'
    ],
    timeline: '2-4 weeks'
  },
  {
    name: 'Feature Build',
    description: 'Add specialized functionality to your existing website with custom-built features tailored to your business needs.',
    features: [
      'Custom functionality development',
      'Database integration',
      'API connections',
      'User authentication',
      'Testing and documentation',
      '60 days of support'
    ],
    timeline: '1-4 weeks depending on complexity'
  },
  {
    name: 'Custom Project',
    description: 'Comprehensive web solutions for complex business requirements. Each project is uniquely designed and built from the ground up.',
    features: [
      'Full custom development',
      'Advanced functionality',
      'Third-party integrations',
      'Performance optimization',
      'Ongoing maintenance options',
      'Dedicated project management'
    ],
    timeline: 'Varies based on scope'
  },
  {
    name: 'Maintenance Plan',
    description: 'Keep your website running smoothly with regular updates, security monitoring, and performance optimization.',
    features: [
      'Monthly security updates',
      'Performance monitoring',
      'Content updates (up to 2 hours)',
      'Backup management',
      'Uptime monitoring',
      'Priority support'
    ],
    timeline: 'Ongoing monthly service'
  },
  {
    name: 'Developer Retainer',
    description: 'Dedicated development time each month for ongoing improvements, bug fixes, and small feature additions.',
    features: [
      '5 hours of development time',
      'Priority scheduling',
      'Rollover unused hours (max 2 months)',
      'Direct developer communication',
      'Monthly progress reports',
      'Flexible project scope'
    ],
    timeline: 'Ongoing monthly service'
  },
  {
    name: 'Emergency Support',
    description: 'Immediate assistance for critical website issues that need urgent attention outside of regular business hours.',
    features: [
      'Same-day response',
      'Critical bug fixes',
      'Security issue resolution',
      'Server troubleshooting',
      'Emergency recovery',
      'After-hours availability'
    ],
    timeline: 'Immediate response'
  }
])

function openModal(service) {
  selectedService.value = service
  showModal.value = true
  document.body.style.overflow = 'hidden'
}

function closeModal() {
  showModal.value = false
  selectedService.value = null
  document.body.style.overflow = ''
}

function contactUs() {
  // Show loading state
  isContactingUs.value = true

  // Close the modal first
  closeModal()

  // Use setTimeout to ensure the modal has closed and DOM has updated
  setTimeout(() => {
    // Reset loading state
    isContactingUs.value = false

    // First try to find the contact form element directly
    let targetElement = document.getElementById('contact-form')

    // If that doesn't exist, try the contact section
    if (!targetElement) {
      targetElement = document.getElementById('contact-section')
    }

    // If we still don't have an element, try the contact form container class
    if (!targetElement) {
      targetElement = document.querySelector('.contact-form-container')
    }

    if (targetElement) {
      // Smooth scroll to the contact form/section
      targetElement.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      })

      // Add highlight effect to the contact form
      const contactForm = targetElement.querySelector('.contact-form') || targetElement
      if (contactForm) {
        contactForm.classList.add('highlight')
        // Remove highlight class after animation completes
        setTimeout(() => {
          contactForm.classList.remove('highlight')
        }, 2000)
      }

      // Focus on the first input field in the contact form
      const firstInput = targetElement.querySelector('input[type="text"], input[type="email"], input[type="tel"], input#name')
      if (firstInput) {
        setTimeout(() => {
          firstInput.focus()
        }, 500) // Small delay to ensure smooth scroll completes
      }
    } else {
      // Fallback: try to find any contact form on the page
      const fallbackForm = document.querySelector('.contact-form input[type="text"], .contact-form input[type="email"]')
      if (fallbackForm) {
        fallbackForm.scrollIntoView({ behavior: 'smooth', block: 'start' })
        setTimeout(() => {
          fallbackForm.focus()
        }, 500)
      }
    }
  }, 100) // Small delay to ensure modal close animation completes
}

// Add keyboard support for closing modal with Escape key
function handleEscapeKey(event) {
  if (event.key === 'Escape' && showModal.value) {
    closeModal()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey)
})
</script>
