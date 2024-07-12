<script setup>
import {useTheme} from "vuetify";
import {onMounted, ref} from "vue";

const theme = useTheme();
const localTheme = ref({
    value: null
});

onMounted(() => {
    localTheme.value = localStorage.getItem('gatekeeper:theme') ?? 'light';
    theme.global.name.value = localTheme.value;
})
</script>
<template>
    <v-app :theme="theme.global.current.value.dark ? 'dark' : 'light'">
        <v-container class="fill-height d-flex flex-column justify-center align-center align-content-center">
            <slot name="logo"/>
            <v-card class="my-6" width="512">
                <slot/>
            </v-card>
            <slot name="after"/>
        </v-container>
    </v-app>
</template>
<style>
.gatekeeper-icon {
    max-width: 100%;
    width: 256px;
    margin: 0 auto;
    fill: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity))
}
</style>
