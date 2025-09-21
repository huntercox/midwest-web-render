<template>
  <MainLayout title="Edit Profile" class="profile-edit">
    <template #header>
      <h2>
        Edit Profile
      </h2>
    </template>

    <div class="container">
      <div class="profile-edit__form">
        <h3>Update your profile information</h3>
        <p>Make sure to keep your information up to date.</p>

        <form @submit.prevent="submit">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" v-model="form.name" class="form-control" />
            <div v-if="form.errors.name">{{ form.errors.name }}</div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" v-model="form.email" class="form-control" />
            <div v-if="form.errors.email">{{ form.errors.email }}</div>
          </div>

          <div>
            <button type="submit" :disabled="form.processing" class="btn btn-primary">
              Update Profile
            </button>
          </div>
        </form>

        <!-- Password Update Section -->
        <div class="profile-edit__password-section" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
          <h3>Update Password</h3>
          <p>Ensure your account is using a long, random password to stay secure.</p>

          <form @submit.prevent="submitPassword">
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input type="password" id="current_password" v-model="passwordForm.current_password" class="form-control" autocomplete="current-password" />
              <div v-if="passwordForm.errors.current_password">{{ passwordForm.errors.current_password }}</div>
            </div>

            <div class="form-group">
              <label for="password">New Password</label>
              <input type="password" id="password" v-model="passwordForm.password" class="form-control" autocomplete="new-password" />
              <div v-if="passwordForm.errors.password">{{ passwordForm.errors.password }}</div>
            </div>

            <div class="form-group">
              <label for="password_confirmation">Confirm Password</label>
              <input type="password" id="password_confirmation" v-model="passwordForm.password_confirmation" class="form-control" autocomplete="new-password" />
              <div v-if="passwordForm.errors.password_confirmation">{{ passwordForm.errors.password_confirmation }}</div>
            </div>

            <div>
              <button type="submit" :disabled="passwordForm.processing" class="btn btn-primary">
                Update Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue'; // Changed AppLayout to MainLayout

const props = defineProps({
  user: Object, // Assuming user data is passed as a prop
  // Add any other props your component might need, like errors
  errors: Object,
});

const form = useForm({
  name: props.user.name,
  email: props.user.email,
});

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  console.log('Submitting profile form...', {
    formData: form.data(),
    route: route('profile.update'),
    processing: form.processing
  });

  form.patch(route('profile.update'), {
    onStart: () => {
      console.log('Form submission started');
    },
    onSuccess: (page) => {
      console.log('Form submission successful', page);
    },
    onError: (errors) => {
      console.error('Form submission errors', errors);
    },
    onFinish: () => {
      console.log('Form submission finished');
    }
  });
};

const submitPassword = () => {
  console.log('Submitting password form...', {
    formData: passwordForm.data(),
    processing: passwordForm.processing,
    route: route('profile.updatePassword')
  });

  passwordForm.patch(route('profile.updatePassword'), {
    onStart: () => {
      console.log('Password form submission started');
      passwordForm.processing = true;
    },
    onSuccess: (page) => {
      console.log('Password updated successfully', page);
      // Clear the password form after successful update
      passwordForm.reset();
      passwordForm.processing = false;
    },
    onError: (errors) => {
      console.error('Password form submission errors', errors);
      passwordForm.setError(errors);
      passwordForm.processing = false;
    },
    onFinish: () => {
      console.log('Password form submission finished');
      passwordForm.processing = false;
    }
  });
};
</script>
