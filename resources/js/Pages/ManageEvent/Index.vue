<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    team: Object,
    events: Array,
})
</script>
<template>
    <AppLayout title="Manage Events">
        <template #header>
            <h1>Manage {{team.name}} Events</h1>
        </template>
        <v-container>
            <v-row justify="end" color="transparent" class="mb-4">
                <v-btn type="button" color="primary" :href="route('manage-event.create')">
                    Create New Event
                </v-btn>
            </v-row>
            <v-table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Policies</th>
                    <th>Tickets</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="event in events" :key="event.uuid">
                    <td>{{event.name}}</td>
                    <td>{{event.event_date}}</td>
                    <td>{{event.policies_count}}</td>
                    <td>{{event.tickets_count}}</td>
                    <td class="text-end">
                        <v-btn :href="route('manage-event.show', event.uuid)" class="me-2">View</v-btn>
                        <v-btn :href="route('manage-event.edit', event.uuid)" class="me-2">Edit</v-btn>
                    </td>
                </tr>
                </tbody>
            </v-table>
<!--            <pre>{{events}}</pre>-->
        </v-container>
    </AppLayout>
</template>
