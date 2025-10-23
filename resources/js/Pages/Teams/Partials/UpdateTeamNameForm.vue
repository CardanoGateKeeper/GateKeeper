<script setup>
import {useForm} from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';

const props = defineProps({
    team: Object,
    permissions: Object,
});

const form = useForm({
    name: props.team.name,
});

const updateTeamName = () => {
    form.put(route('teams.update', props.team), {
        errorBag: 'updateTeamName',
        preserveScroll: true,
    });
};

const rules = {
    required: (v) => !!v || 'Field is required'
}
</script>
<template>
    <FormSection @submitted="updateTeamName">
        <template #title>
            Team Name
        </template>
        <template #description>
            The team's name and owner information.
        </template>
        <template #form>
            <!-- Team Owner Information -->
            <v-banner :avatar="team.owner.profile_photo_url">
                <v-banner-text>
                    Team Owner
                    <br/>
                    <b>{{ team.owner.name }}</b>
                    <br/>
                    <i>{{ team.owner.email }}</i>
                </v-banner-text>
            </v-banner>
            <!-- Team Name -->
            <v-text-field label="Team Name" v-model="form.name" type="text" v-if="permissions.canUpdateTeam"
                          :rules="[rules.required]"/>
            <h2 v-else class="my-4">{{ form.name }}</h2>
        </template>
        <template v-if="permissions.canUpdateTeam" #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>
            <v-btn type="submit" :loading="form.processing" :disabled="form.hasErrors" color="primary">Save</v-btn>
        </template>
    </FormSection>
</template>
