<template>
	<MainLayout>
		<template #header>
			<h2>Contact Us</h2>
		</template>

		<div class="contact-page">
			<div class="contact-info">
				<h3>Get in Touch</h3>
				<p>Please fill out the form below and we'll get back to you as soon as possible.</p>

				<div v-if="$page.props.flash.success" class="alert alert-success">
					{{ $page.props.flash.success }}
				</div>

				<div v-if="$page.props.flash.error" class="alert alert-danger">
					{{ $page.props.flash.error }}
				</div>
			</div>

			<div class="contact-form">
				<ContactForm />
			</div>
		</div>
	</MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import ContactForm from '@/Components/ContactForm.vue';
</script>
