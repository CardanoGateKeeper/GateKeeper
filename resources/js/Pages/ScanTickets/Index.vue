<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
  team: Object,
  events: Array,
});
</script>
<template>
  <AppLayout title="Scan Tickets">
    <template #header>
      <h1>Scan Tickets for {{team.name}} Events</h1>
    </template>
    <v-container>
      <v-row justify="end" color="transparent" class="mb-4">
        <!-- Not sure we need any action buttons here but just in case... -->
      </v-row>
      <v-table>
        <thead>
        <tr>
          <th>Name</th>
          <th>Date</th>
          <th class="text-end">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="event in events" :key="event.uuid">
          <td>{{event.name}}</td>
          <td>{{event.event_date}}</td>
          <td class="text-end">
            <v-btn color="primary" :href="route('scan-tickets.show', event.uuid)" class="me-2">
              Scan
            </v-btn>
          </td>
        </tr>
        </tbody>
      </v-table>
    </v-container>
  </AppLayout>
</template>
