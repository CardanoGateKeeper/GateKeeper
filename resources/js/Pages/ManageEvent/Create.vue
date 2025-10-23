<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EventDetailsForm
    from "@/Pages/ManageEvent/Partials/EventDetailsForm.vue";

const default_event = {
    name: null,
    nonce_valid_for_minutes: 15,
    hodl_asset: false,
    start_date_time: null,
    end_date_time: null,
    location: null,
    event_start: null,
    event_end: null,
    event_date: null,
};
</script>
<template>
    <AppLayout title="Create New Event">
        <template #header>
            <h1>Create New Event</h1>
        </template>
        <v-container>
            <EventDetailsForm :event="default_event"/>
        </v-container>
    </AppLayout>
</template>
