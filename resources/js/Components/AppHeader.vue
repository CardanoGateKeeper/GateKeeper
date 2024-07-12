<script setup>
import { useTheme } from 'vuetify'
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {onMounted, ref} from "vue";

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
.v-toolbar-title .gatekeeper-logo {
    max-height: 50px;
    width: auto;
    margin-right: 0.5em;
    fill: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity))
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
    <v-toolbar class="d-flex flex-row justify-between" color="transparent">
        <v-toolbar-title>
            <ApplicationLogo></ApplicationLogo>
            <span class="app-name">GateKeeper</span>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
            <v-btn @click="toggleTheme">
                <v-icon :icon="theme.global.current.value.dark ? 'mdi-weather-sunny' : 'mdi-weather-night'" />
            </v-btn>
            <template v-if="canLogin">
                <v-btn color="primary" :href="route('dashboard')" v-if="$page.props.auth.user">
                    Dashboard
                </v-btn>
                <template v-else>
                    <v-btn color="primary" :href="route('login')">Log In</v-btn>
                    <v-btn color="secondary" v-if="canRegister" :href="route('register')">Register</v-btn>
                </template>
            </template>
        </v-toolbar-items>
    </v-toolbar>
</template>
