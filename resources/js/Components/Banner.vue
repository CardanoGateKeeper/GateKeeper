<script setup>
import {ref, watchEffect} from 'vue';
import {usePage} from '@inertiajs/vue3';

const page = usePage();
const show = ref(true);
const style = ref('success');
const message = ref('');

watchEffect(async () => {
  style.value = page.props.jetstream.flash?.bannerStyle || 'success';
  message.value = page.props.jetstream.flash?.banner || '';
  show.value = true;
});
</script>

<template>
  <v-alert v-if="show && message" :type="style" closable class="my-8">
    {{ message }}
  </v-alert>
</template>
