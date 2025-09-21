<template>
	<MainLayout class="contact-demo">
		<template #header>
			<h2>Contact Form (3 Lines of Validation Code!)</h2>
		</template>

		<form @submit.prevent="submit" novalidate>
			<!-- Name -->
			<div class="form-group">
				<label for="name">Name *</label>
				<input v-model="name" v-bind="nameAttrs" :class="getFieldClasses('name')" id="name" type="text" />
				<div v-if="hasError('name')" class="error">{{ errors.name }}</div>
			</div>

			<!-- Email -->
			<div class="form-group">
				<label for="email">Email *</label>
				<input v-model="email" v-bind="emailAttrs" :class="getFieldClasses('email')" id="email" type="email" />
				<div v-if="hasError('email')" class="error">{{ errors.email }}</div>
			</div>

			<!-- Subject -->
			<div class="form-group">
				<label for="subject">Subject *</label>
				<input v-model="subject" v-bind="subjectAttrs" :class="getFieldClasses('subject')" id="subject" type="text" />
				<div v-if="hasError('subject')" class="error">{{ errors.subject }}</div>
			</div>

			<!-- Message -->
			<div class="form-group">
				<label for="message">Message *</label>
				<textarea v-model="message" v-bind="messageAttrs" :class="getFieldClasses('message')" id="message" rows="5"></textarea>
				<div v-if="hasError('message')" class="error">{{ errors.message }}</div>
			</div>

			<!-- Submit -->
			<button type="submit" :disabled="!isFormValid">
				{{ isSubmitting ? 'Sending...' : 'Send Message' }}
			</button>
		</form>
	</MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { useFormValidation } from '../composables/useFormValidation.js'

const isSubmitting = ref(false)

// 🎉 ENTIRE VALIDATION SYSTEM IN 3 LINES:
const { errors, createField, hasError, getFieldClasses, submit, isFormValid } = useFormValidation('contact', {}, handleSubmit)
const [name, nameAttrs] = createField('name')
const [email, emailAttrs] = createField('email')
const [subject, subjectAttrs] = createField('subject')
const [message, messageAttrs] = createField('message')
// ⬆️ That's it! Full validation with real-time feedback, error handling, and accessibility

async function handleSubmit(data) {
	isSubmitting.value = true
	try {
		await router.post('/contact', data)
	} finally {
		isSubmitting.value = false
	}
}
</script>

<style lang="scss" scoped>
.contact-demo {
	max-width: 600px;
	margin: 0 auto;
	padding: 2rem;
}

.form-group {
	margin-bottom: 1rem;
}

label {
	display: block;
	margin-bottom: 0.5rem;
	font-weight: 600;
}

input,
textarea {
	width: 100%;
	padding: 0.75rem;
	border: 2px solid #ddd;
	border-radius: 0.25rem;

	&.is-valid {
		border-color: #28a745;
	}
	&.is-invalid {
		border-color: #dc3545;
	}
}

button {
	background: #007bff;
	color: white;
	padding: 0.75rem 1.5rem;
	border: none;
	border-radius: 0.25rem;
	cursor: pointer;

	&:disabled {
		background: #6c757d;
		cursor: not-allowed;
	}
}

.error {
	color: #dc3545;
	font-size: 0.875rem;
	margin-top: 0.25rem;
}
</style>
