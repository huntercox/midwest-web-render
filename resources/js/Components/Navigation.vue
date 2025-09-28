<template>
  <nav class="site-nav">
    <div class="nav-container">
      <!-- Desktop Navigation Links -->
      <div class="nav-links" v-if="authUser">
        <Link
              v-for="item in navItems"
              :key="item.name"
              :href="item.href"
              :class="{ active: item.active }">
        {{ item.name }}
        </Link>
      </div>

      <Link
            :href="route('logout')"
            v-if="authUser"
            method="post"
            as="button"
            class="tooltip-wrapper"
            title="Log Out"
            aria-label="Log Out">
      <SignOutIcon class="button--loginout" />
      <span class="sr-only">Log Out</span>
      <span class="tooltip-text">Log Out</span>
      </Link>


      <Link
            v-else
            :href="route('login')"
            as="button"
            class="tooltip-wrapper"
            title="Log In"
            aria-label="Log In">
      <SignInIcon class="button--loginout" />
      <span class="sr-only">Log In</span>
      <span class="tooltip-text">Log In</span>
      </Link>

      <!-- Mobile Menu Toggle
      <button class="menu-toggle" @click="open = !open">
        <span v-if="!open">Menu</span>
        <span v-else>Close</span>
      </button> -->

      <!-- Mobile Navigation
      <div v-if="open" class="mobile-menu">
        <div class="mobile-links">
          <Link
                v-for="item in navItems"
                :key="item.name"
                :href="item.href"
                :class="{ active: item.active }"
                @click="open = false">
          {{ item.name }}
          </Link>
          <Link
                v-if="authUser"
                :href="route('logout')"
                method="post"
                as="button"
                class="button"
                @click="open = false">
          Log Out
          </Link>
          <Link
                v-else
                :href="route('login')"
                class="button"
                @click="open = false">
          Log In
          </Link>
        </div>
      </div>-->

    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

import SignInIcon from '@/Components/Icons/SignInIcon.vue';
import SignOutIcon from '@/Components/Icons/SignOutIcon.vue';

const open = ref(false);
const page = usePage();

// Determine if user is authenticated
const authUser = computed(() => Boolean(page.props.auth?.user));

// Define nav items, only used if user is logged in
const navItems = computed(() => authUser.value ? [
  {
    name: 'Dashboard',
    href: route('dashboard'),
    active: page.url.startsWith('/dashboard')
  },
  {
    name: 'Edit Profile',
    href: route('profile.edit'),
    active: page.url.startsWith('/edit-profile'),
  },
] : []);
</script>

<style scoped lang="scss">
.site-nav {
  width: 100%;
}

.nav-container {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 0; // Remove default padding since container handles it
  gap: var(--space-sm);

  @media (min-width: var(--breakpoint-md)) {
    gap: var(--space-md);
  }
}

.nav-links {
  // Mobile-first: hide by default
  display: none;

  // Show on tablet and up
  @media (min-width: var(--breakpoint-md)) {
    display: flex;
    gap: var(--space-md);
    align-items: center;
  }

  a {
    text-decoration: none;
    color: inherit;
    position: relative;
    padding: var(--space-xs) var(--space-sm);
    border-radius: var(--radius-sm);
    transition: var(--transition-fast);
    font-size: var(--font-size-sm);

    @media (min-width: var(--breakpoint-lg)) {
      font-size: var(--font-size-base);
      padding: var(--space-sm) var(--space-md);
    }

    &:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
  }

  .active {
    position: relative;
    font-weight: bold;
    background-color: rgba(255, 255, 255, 0.1);

    &::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 50%;
      transform: translateX(-50%);
      width: 80%;
      height: 2px;
      background-color: var(--color-green);
      border-radius: var(--radius-full);
    }
  }
}

// Login/logout button (always visible)
.tooltip-wrapper {
  position: relative;
  padding: var(--space-xs);
  border-radius: var(--radius-sm);
  transition: var(--transition-fast);

  &:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }

  .button--loginout {
    width: 24px;
    height: 24px;

    @media (min-width: var(--breakpoint-md)) {
      width: 1.5rem;
      height: 1.5rem;
    }
  }
}

.menu-toggle {
  // Mobile-first: show by default
  display: flex;
  align-items: center;
  justify-content: center;
  background: none;
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: inherit;
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-sm);
  font-size: var(--font-size-sm);
  cursor: pointer;
  transition: var(--transition-fast);

  // Hide on tablet and up
  @media (min-width: var(--breakpoint-md)) {
    display: none;
  }

  &:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
  }

  &:active {
    transform: scale(0.95);
  }
}

.mobile-menu {
  // Mobile-first: hidden by default, shown when open
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: var(--color-black);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: var(--shadow-lg);
  z-index: 50;

  // Hide on tablet and up
  @media (min-width: var(--breakpoint-md)) {
    display: none !important;
  }

  .mobile-links {
    display: flex;
    flex-direction: column;
    padding: var(--space-md) var(--space-sm);
    gap: var(--space-xs);

    a {
      text-decoration: none;
      color: inherit;
      padding: var(--space-sm) var(--space-md);
      border-radius: var(--radius-sm);
      transition: var(--transition-fast);
      font-size: var(--font-size-base);

      &:hover {
        background-color: rgba(255, 255, 255, 0.1);
      }

      &.active {
        background-color: rgba(255, 255, 255, 0.1);
        font-weight: bold;
        border-left: 3px solid var(--color-green);
      }
    }

    .button {
      background-color: var(--color-green);
      color: var(--color-black);
      border: none;
      padding: var(--space-sm) var(--space-md);
      border-radius: var(--radius-sm);
      font-weight: bold;
      text-align: center;
      margin-top: var(--space-sm);
      transition: var(--transition-fast);

      &:hover {
        background-color: color-mix(in srgb, var(--color-green), var(--color-white) 20%);
      }
    }
  }
}
</style>