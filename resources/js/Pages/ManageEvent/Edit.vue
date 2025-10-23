<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EventDetailsForm from "@/Pages/ManageEvent/Partials/EventDetailsForm.vue";
import EventPoliciesTable from "@/Pages/ManageEvent/Partials/EventPoliciesTable.vue";

import {usePage} from '@inertiajs/vue3';
import {computed} from 'vue';

const page = usePage();

const flash = computed(() => page.props.flash);

defineProps({
  event: Object,
  team_policies: Array,
});
</script>
<template>
  <AppLayout title="Edit Event">
    <template #header>
      <h1>Edit Event</h1>
    </template>
    <v-container>

      <v-snackbar v-model="flash.message" location="top right" rounded color="primary">
        {{ flash.message }}
      </v-snackbar>
      <v-card elevation="0">
        <v-card-title>Event Details</v-card-title>
        <v-card-text>
          <EventDetailsForm :event="event"/>
        </v-card-text>
      </v-card>
      <v-card elevation="0">
        <v-card-title>Event Policies</v-card-title>
        <v-card-text>
          <EventPoliciesTable :event="event"
                              :team_policies="team_policies"/>
        </v-card-text>
      </v-card>
    </v-container>
  </AppLayout>
</template>
