<script setup>
import {onMounted, ref} from 'vue';
import {Head} from '@inertiajs/vue3';
import {useTheme} from "vuetify";
import NetworkNotice from "@/Components/NetworkNotice.vue";

defineProps({
  title: String
});

const theme = useTheme();

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

</style>
<template>
  <Head :title="title"></Head>
  <v-app>
    <NetworkNotice />
    <v-main>
      <slot name="header"></slot>
      <slot></slot>
    </v-main>
  </v-app>
</template>
