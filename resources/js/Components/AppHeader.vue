<script setup>
import {useTheme} from 'vuetify'
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {onMounted, ref} from "vue";

import { router } from '@inertiajs/vue3'

const logout = () => {
  router.post(route('logout'))
}

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  }
});

const theme = useTheme()

function toggleTheme() {
  const theme_value = theme.global.current.value.dark ? 'light' : 'dark'
  localStorage.setItem('gatekeeper:theme', theme_value);
  theme.global.name.value = theme_value;
}

const localTheme = ref({
  value: null
});

onMounted(() => {
  localTheme.value = localStorage.getItem('gatekeeper:theme') ?? 'light';
  theme.global.name.value = localTheme.value;
})
</script>
<style>
.v-toolbar#app-bar {
  color: white !important;
}
.v-toolbar-title .gatekeeper-logo {
  max-height: 50px;
  width: auto;
  margin-right: 0.5em;
  fill: white
}

.v-toolbar-title__placeholder {
  display: flex;
  align-items: center;
}

.app-name {
  font-family: "Open Sans", sans-serif;
  font-weight: 600;
  font-size: 24px;
}
</style>
<template>
  <v-toolbar id="app-bar" class="d-flex flex-row justify-between" color="transparent">
    <v-toolbar-title @click="$router.push('/')">
      <ApplicationLogo></ApplicationLogo>
      <span class="app-name">GateKeeper</span>
    </v-toolbar-title>
    <v-spacer></v-spacer>
    <v-toolbar-items>
      <v-btn @click="toggleTheme">
        <v-icon
          :icon="theme.global.current.value.dark ? 'mdi-weather-sunny' : 'mdi-weather-night'"/>
      </v-btn>
      <v-btn :href="route('home')">
        <v-icon icon="mdi-home"/>
      </v-btn>
      <v-btn :href="route('events.discover')" prepend-icon="mdi-creation">
        Discover Events
      </v-btn>
      <template v-if="canLogin">
        <template v-if="$page.props.auth.user">
          <v-btn color="primary" :href="route('dashboard')">
            Dashboard
          </v-btn>
          <v-btn
            color="secondary"
            prepend-icon="mdi-logout"
            @click="logout"
          >
            Logout
          </v-btn>
        </template>
        <template v-else>
          <v-btn
            color="primary"
            prepend-icon="mdi-login"
            :href="route('login')">Staff Log In</v-btn>
          <v-btn color="secondary" prepend-icon="mdi-account-plus" v-if="canRegister" :href="route('register')">
            Register
          </v-btn>
        </template>
      </template>
    </v-toolbar-items>
  </v-toolbar>
</template>
