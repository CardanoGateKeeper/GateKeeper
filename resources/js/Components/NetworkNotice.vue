<script setup>
import {computed} from "vue";

const appEnv = import.meta.env.VITE_APP_ENV || 'production'
const isPreprod = computed(() => appEnv === 'preprod')
</script>
<template>
  <v-system-bar window
    v-if="isPreprod"
    color="warning"
    density="compact"
    class="align-center">
    <v-spacer/>
    <p>This is a demo / pre-production instance of GateKeeper.
      Any data you enter here may be reset automatically at
      &nbsp;<b>00:00 UTC</b> &nbsp;each day.</p>
    <v-spacer/>
  </v-system-bar>
</template>
