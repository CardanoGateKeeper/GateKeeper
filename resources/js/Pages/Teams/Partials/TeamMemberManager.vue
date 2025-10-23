<script setup>
import {ref} from 'vue';
import {router, useForm, usePage} from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DialogModal from '@/Components/DialogModal.vue';
import FormSection from '@/Components/FormSection.vue';
import SectionBorder from '@/Components/SectionBorder.vue';

const props = defineProps({
    team: Object,
    availableRoles: Array,
    userPermissions: Object,
});

const page = usePage();

const addTeamMemberForm = useForm({
    email: '',
    role: null,
});

const updateRoleForm = useForm({
    role: null,
});

const leaveTeamForm = useForm({});
const removeTeamMemberForm = useForm({});

const currentlyManagingRole = ref(false);
const managingRoleFor = ref(null);
const confirmingLeavingTeam = ref(false);
const teamMemberBeingRemoved = ref(null);

const addTeamMember = () => {
    addTeamMemberForm.post(route('team-members.store', props.team), {
        errorBag: 'addTeamMember',
        preserveScroll: true,
        onSuccess: () => addTeamMemberForm.reset(),
    });
};

const cancelTeamInvitation = (invitation) => {
    router.delete(route('team-invitations.destroy', invitation), {
        preserveScroll: true,
    });
};

const manageRole = (teamMember) => {
    managingRoleFor.value = teamMember;
    updateRoleForm.role = teamMember.membership.role;
    currentlyManagingRole.value = true;
};

const updateRole = () => {
    updateRoleForm.put(route('team-members.update', [props.team, managingRoleFor.value]), {
        preserveScroll: true,
        onSuccess: () => currentlyManagingRole.value = false,
    });
};

const confirmLeavingTeam = () => {
    confirmingLeavingTeam.value = true;
};

const leaveTeam = () => {
    leaveTeamForm.delete(route('team-members.destroy', [props.team, page.props.auth.user]));
};

const confirmTeamMemberRemoval = (teamMember) => {
    teamMemberBeingRemoved.value = teamMember;
};

const removeTeamMember = () => {
    removeTeamMemberForm.delete(route('team-members.destroy', [props.team, teamMemberBeingRemoved.value]), {
        errorBag: 'removeTeamMember',
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => teamMemberBeingRemoved.value = null,
    });
};

const displayableRole = (role) => {
    return props.availableRoles.find(r => r.key === role).name;
};

const rules = {
    required: (v) => !!v || 'Field is required'
}
</script>
<template>
    <template v-if="userPermissions.canAddTeamMembers">
        <SectionBorder/>
        <!-- Add Team Member -->
        <FormSection @submitted="addTeamMember">
            <template #title>
                Add Team Member
            </template>
            <template #description>
                Add a new team member to your team, allowing them to collaborate with you.
            </template>
            <template #form>
                <v-alert type="error" class="mb-4" closable v-model="$page.props.errors.addTeamMember"
                         v-if="$page.props.errors.addTeamMember">
                    <v-alert-title>ERROR</v-alert-title>
                    <p v-for="(error,key) in $page.props.errors.addTeamMember">
                        {{ error }}
                    </p>
                </v-alert>
                <!--                    <div class="col-span-6">
                                        <div class="max-w-xl text-sm text-gray-600"></div>
                                    </div>-->
                <!-- Member Email -->
                <v-text-field v-model="addTeamMemberForm.email" type="email" label="Email" :rules="[rules.required]"
                              hint="Please provide the email address of the person you would like to add to this team."
                              persistent-hint class="mb-4"/>
                <!--                    <div class="col-span-6 sm:col-span-4">
                                        <InputLabel for="email" value="Email"/>
                                        <TextInput id="email" v-model="addTeamMemberForm.email" type="email" class="mt-1 block w-full"/>
                                        <InputError :message="addTeamMemberForm.errors.email" class="mt-2"/>
                                    </div>-->
                <!-- Role -->
                <template v-if="availableRoles.length > 0">
                    <p class="text-subtitle-2">Role</p>
                    <v-btn-toggle v-model="addTeamMemberForm.role" mandatory color="primary" variant="outlined"
                                  class="mb-2" divided>
                        <v-btn v-for="(role,i) in availableRoles" :key="role.key" :value="role.key">
                            <template v-slot:append>
                                <v-icon icon="mdi-check-circle-outline" v-if="addTeamMemberForm.role == role.key" />
                            </template>
                            {{ role.name }}
                        </v-btn>
                    </v-btn-toggle>
                </template>
                <!--                    <div v-if="availableRoles.length > 0" class="col-span-6 lg:col-span-4">
                                        <InputLabel for="roles" value="Role"/>
                                        <InputError :message="addTeamMemberForm.errors.role" class="mt-2"/>
                                        <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                                            <button v-for="(role, i) in availableRoles" :key="role.key" type="button"
                                                    class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                                                    :class="{'border-t border-gray-200 focus:border-none rounded-t-none': i > 0, 'rounded-b-none': i != Object.keys(availableRoles).length - 1}"
                                                    @click="addTeamMemberForm.role = role.key">
                                                <div
                                                    :class="{'opacity-50': addTeamMemberForm.role && addTeamMemberForm.role != role.key}">
                                                    &lt;!&ndash; Role Name &ndash;&gt;
                                                    <div class="flex items-center">
                                                        <div class="text-sm text-gray-600"
                                                             :class="{'font-semibold': addTeamMemberForm.role == role.key}">
                                                            {{ role.name }}
                                                        </div>
                                                        <svg v-if="addTeamMemberForm.role == role.key"
                                                             class="ms-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                    &lt;!&ndash; Role Description &ndash;&gt;
                                                    <div class="mt-2 text-xs text-gray-600 text-start">
                                                        {{ role.description }}
                                                    </div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>-->
            </template>
            <template #actions>
                <ActionMessage :on="addTeamMemberForm.recentlySuccessful" class="me-3">
                    Added.
                </ActionMessage>
                <v-btn type="submit" color="primary" :loading="addTeamMemberForm.processing"
                       :disabled="addTeamMemberForm.processing">Add
                </v-btn>
                <!--                    <PrimaryButton :class="{ 'opacity-25': addTeamMemberForm.processing }"
                                                   :disabled="addTeamMemberForm.processing">
                                        Add
                                    </PrimaryButton>-->
            </template>
        </FormSection>
    </template>
    <template v-if="team.team_invitations.length > 0 && userPermissions.canAddTeamMembers">
        <SectionBorder/>
        <!-- Team Member Invitations -->
        <ActionSection>
            <template #title>
                Pending Team Invitations
            </template>
            <template #description>
                These people have been invited to your team and have been sent an invitation email. They may join the
                team by accepting the email invitation.
            </template>
            <!-- Pending Team Member Invitation List -->
            <template #content>
                <v-banner v-for="invitation in team.team_invitations" :key="invitation.id" lines="two"
                          class="align-center">
                    <v-banner-text>
                        <h3>{{ invitation.email }}</h3>
                        <p>{{ invitation.role }}</p>
                    </v-banner-text>
                    <v-spacer></v-spacer>
                    <v-banner-actions class="justify-end">
                        <v-btn color="error" @click="cancelTeamInvitation(invitation)">
                            Cancel
                        </v-btn>
                    </v-banner-actions>
                </v-banner>
                <!--                    <div class="space-y-6">
                                        <div v-for="invitation in team.team_invitations" :key="invitation.id"
                                             class="flex items-center justify-between">
                                            <div class="text-gray-600">
                                                {{ invitation.email }}
                                            </div>
                                            <div class="flex items-center">
                                                &lt;!&ndash; Cancel Team Invitation &ndash;&gt;
                                                <button v-if="userPermissions.canRemoveTeamMembers"
                                                        class="cursor-pointer ms-6 text-sm text-red-500 focus:outline-none"
                                                        @click="cancelTeamInvitation(invitation)">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>-->
            </template>
        </ActionSection>
    </template>
    <template v-if="team.users.length > 0">
        <SectionBorder/>
        <!-- Manage Team Members -->
        <ActionSection class="mt-10 sm:mt-0">
            <template #title>
                Team Members
            </template>
            <template #description>
                All of the people that are part of this team.
            </template>
            <!-- Team Member List -->
            <template #content>
                <v-banner :avatar="user.profile_photo_url" v-for="user in team.users" :key="user.id" lines="three">
                    <v-banner-text>
                        <h3>{{ user.name }}</h3>
                        <v-btn variant="text" v-if="userPermissions.canUpdateTeamMembers && availableRoles.length"
                               @click="manageRole(user)" append-icon="mdi-pencil">
                            {{ displayableRole(user.membership.role) }}
                        </v-btn>
                        <v-btn variant="text" v-else disabled>
                            {{ displayableRole(user.membership.role) }}
                        </v-btn>
                    </v-banner-text>
                    <v-spacer></v-spacer>
                    <v-banner-actions>
                        <v-btn v-if="$page.props.auth.user.id === user.id" color="error" @click="confirmLeavingTeam"
                               class="ms-4">
                            Leave
                        </v-btn>
                        <v-btn v-else-if="userPermissions.canRemoveTeamMembers" @click="confirmTeamMemberRemoval(user)"
                               color="error" class="ms-4">
                            Remove
                        </v-btn>
                    </v-banner-actions>
                </v-banner>
                <!--                    <div class="space-y-6">
                                        <div v-for="user in team.users" :key="user.id" class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <img class="w-8 h-8 rounded-full object-cover" :src="user.profile_photo_url"
                                                     :alt="user.name">
                                                <div class="ms-4">
                                                    {{ user.name }}
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                &lt;!&ndash; Manage Team Member Role &ndash;&gt;
                                                <button v-if="userPermissions.canUpdateTeamMembers && availableRoles.length"
                                                        class="ms-2 text-sm text-gray-400 underline" @click="manageRole(user)">
                                                    {{ displayableRole(user.membership.role) }}
                                                </button>
                                                <div v-else-if="availableRoles.length" class="ms-2 text-sm text-gray-400">
                                                    {{ displayableRole(user.membership.role) }}
                                                </div>
                                                &lt;!&ndash; Leave Team &ndash;&gt;
                                                <button v-if="$page.props.auth.user.id === user.id"
                                                        class="cursor-pointer ms-6 text-sm text-red-500" @click="confirmLeavingTeam">
                                                    Leave
                                                </button>
                                                &lt;!&ndash; Remove Team Member &ndash;&gt;
                                                <button v-else-if="userPermissions.canRemoveTeamMembers"
                                                        class="cursor-pointer ms-6 text-sm text-red-500"
                                                        @click="confirmTeamMemberRemoval(user)">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>-->
            </template>
        </ActionSection>
    </template>
    <!-- Role Management Modal -->
    <DialogModal :show="currentlyManagingRole" @close="currentlyManagingRole = false">
        <template #title>
            Manage Role
        </template>
        <template #content>
            <v-list v-model="updateRoleForm.role" density="comfortable">
                <v-list-subheader>ROLES</v-list-subheader>
                <v-list-item v-for="(role,i) in availableRoles" color="primary"
                             :active="updateRoleForm.role == role.key" :key="i" :title="role.name"
                             :subtitle="role.description" @click="updateRoleForm.role = role.key">
                    <template v-slot:append>
                        <v-icon icon="mdi-check-circle-outline" v-if="updateRoleForm.role == role.key"/>
                    </template>
                </v-list-item>
            </v-list>
            <!--                <v-tabs v-model="updateRoleForm.role" direction="vertical" color="primary">
                                <v-tab :value="role.key" v-for="(role,i) in availableRoles">
                                    <template v-slot:prepend>
                                        <v-icon icon="mdi-check-circle-outline" v-if="updateRoleForm.role == role.key"></v-icon>
                                    </template>
                                    <p class="text-start">{{ role.name }}</p>
                                    <p class="text-start">{{ role.description}}</p>
                                </v-tab>
                            </v-tabs>-->
            <!--                <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                                <button v-for="(role, i) in availableRoles" :key="role.key" type="button"
                                        class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                                        :class="{'border-t border-gray-200 focus:border-none rounded-t-none': i > 0, 'rounded-b-none': i !== Object.keys(availableRoles).length - 1}"
                                        @click="updateRoleForm.role = role.key">
                                    <div :class="{'opacity-50': updateRoleForm.role && updateRoleForm.role !== role.key}">
                                        &lt;!&ndash; Role Name &ndash;&gt;
                                        <div class="flex items-center">
                                            <div class="text-sm text-gray-600"
                                                 :class="{'font-semibold': updateRoleForm.role === role.key}">
                                                {{ role.name }}
                                            </div>
                                            <svg v-if="updateRoleForm.role == role.key" class="ms-2 h-5 w-5 text-green-400"
                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        &lt;!&ndash; Role Description &ndash;&gt;
                                        <div class="mt-2 text-xs text-gray-600">
                                            {{ role.description }}
                                        </div>
                                    </div>
                                </button>
                            </div>-->
        </template>
        <template #footer>
            <v-btn @click="currentlyManagingRole = false">Cancel</v-btn>
            <v-spacer></v-spacer>
            <!--                <SecondaryButton @click="currentlyManagingRole = false">
                                Cancel
                            </SecondaryButton>-->
            <!--                <PrimaryButton class="ms-3" :class="{ 'opacity-25': updateRoleForm.processing }"
                                           :disabled="updateRoleForm.processing" @click="updateRole">
                                Save
                            </PrimaryButton>-->
            <v-btn color="primary" @click="updateRole" :loading="updateRoleForm.processing"
                   :disabled="updateRoleForm.processing">
                Save
            </v-btn>
        </template>
    </DialogModal>
    <!-- Leave Team Confirmation Modal -->
    <ConfirmationModal :show="confirmingLeavingTeam" @close="confirmingLeavingTeam = false">
        <template #title>
            Leave Team
        </template>
        <template #content>
            Are you sure you would like to leave this team?
        </template>
        <template #footer>
            <v-btn @click="confirmingLeavingTeam = false">Cancel</v-btn>
            <v-spacer/>
            <v-btn color="error" @click="leaveTeam" :loading="leaveTeamForm.processing"
                   :disabled="leaveTeamForm.processing">
                Leave
            </v-btn>
            <!--                <SecondaryButton @click="confirmingLeavingTeam = false">
                                Cancel
                            </SecondaryButton>
                            <DangerButton class="ms-3" :class="{ 'opacity-25': leaveTeamForm.processing }"
                                          :disabled="leaveTeamForm.processing" @click="leaveTeam">
                                Leave
                            </DangerButton>-->
        </template>
    </ConfirmationModal>
    <!-- Remove Team Member Confirmation Modal -->
    <ConfirmationModal :show="teamMemberBeingRemoved" @close="teamMemberBeingRemoved = null">
        <template #title>
            Remove Team Member
        </template>
        <template #content>
            Are you sure you would like to remove this person from the team?
        </template>
        <template #footer>
            <v-btn @click="teamMemberBeingRemoved = null">Cancel</v-btn>
            <v-spacer/>
            <v-btn color="error" @click="removeTeamMember" :loading="removeTeamMemberForm.processing"
                   :disabled="removeTeamMemberForm.processing">
                Remove
            </v-btn>
            <!--                <SecondaryButton @click="teamMemberBeingRemoved = null">
                                Cancel
                            </SecondaryButton>
                            <DangerButton class="ms-3" :class="{ 'opacity-25': removeTeamMemberForm.processing }"
                                          :disabled="removeTeamMemberForm.processing" @click="removeTeamMember">
                                Remove
                            </DangerButton>-->
        </template>
    </ConfirmationModal>
</template>
