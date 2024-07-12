<script setup>
// import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>
<style>
svg path {
    fill: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity))
}

.v-card {
    border-bottom-width: 6px;
}

.v-card.v-card--variant-outlined {
    border-color: rgb(var(--v-theme-primary))
}

.v-card.v-card--variant-outlined.text-primary {
    color: rgb(var(--v-theme-on-surface)) !important
}
</style>
<template>
    <div>
        <v-row>
            <v-col cols="12" md="6">
                <v-card variant="outlined" color="primary" class="text-center"
                        :href="route('scan-tickets.index')">
                    <v-card-text>
                        <v-icon size="128" icon="mdi-ticket" class="py-8"/>
                    </v-card-text>
                    <v-card-title>
                        Scan Tickets
                    </v-card-title>
                </v-card>
            </v-col>
            <v-col cols="12" md="6">
                <v-card variant="outlined" color="primary" class="text-center"
                        :href="route('manage-event.index')">
                    <v-card-text>
                        <v-icon size="128" icon="mdi-calendar-star"
                                class="py-8"/>
                    </v-card-text>
                    <v-card-title>
                        Manage Events
                    </v-card-title>
                </v-card>
            </v-col>
            <v-col cols="12" md="6">
                <v-card variant="outlined" color="primary" class="text-center"
                        :href="route('teams.show', $page.props.auth.user.current_team)">
                    <v-card-text>
                        <v-icon size="128" icon="mdi-account-multiple"
                                class="py-8"/>
                    </v-card-text>
                    <v-card-title>
                        Manage Team
                    </v-card-title>
                </v-card>
            </v-col>
            <v-col cols="12" md="6">
                <v-card variant="outlined" color="primary" class="text-center"
                        :href="route('profile.show')">
                    <v-card-text>
                        <v-icon size="128" icon="mdi-account" class="py-8"/>
                    </v-card-text>
                    <v-card-title>
                        Manage Your Profile
                    </v-card-title>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>
