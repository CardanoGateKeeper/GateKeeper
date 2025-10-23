<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h1>Dashboard</h1>
        </template>

        <v-container class="py-12">
            <Welcome />
        </v-container>
    </AppLayout>
</template>
