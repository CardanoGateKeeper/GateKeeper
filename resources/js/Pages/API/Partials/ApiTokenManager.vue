<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import Checkbox from '@/Components/Checkbox.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  tokens: Array,
  availablePermissions: Array,
  defaultPermissions: Array,
});

const createApiTokenForm = useForm({
  name: '',
  permissions: props.defaultPermissions,
});

const updateApiTokenForm = useForm({
  permissions: [],
});

const deleteApiTokenForm = useForm({});

const displayingToken = ref(false);
const managingPermissionsFor = ref(null);
const apiTokenBeingDeleted = ref(null);

const createApiToken = () => {
  createApiTokenForm.post(route('api-tokens.store'), {
    preserveScroll: true,
    onSuccess: () => {
      displayingToken.value = true;
      createApiTokenForm.reset();
    },
  });
};

const manageApiTokenPermissions = (token) => {
  updateApiTokenForm.permissions = token.abilities;
  managingPermissionsFor.value = token;
};

const updateApiToken = () => {
  updateApiTokenForm.put(route('api-tokens.update', managingPermissionsFor.value), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => (managingPermissionsFor.value = null),
  });
};

const confirmApiTokenDeletion = (token) => {
  apiTokenBeingDeleted.value = token;
};

const deleteApiToken = () => {
  deleteApiTokenForm.delete(route('api-tokens.destroy', apiTokenBeingDeleted.value), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => (apiTokenBeingDeleted.value = null),
  });
};
</script>

<template>
  <!-- Generate API Token -->
  <FormSection @submitted="createApiToken">
    <template #title>
      Create API Token
    </template>

    <template #description>
      API tokens allow third-party services to authenticate with our application on your behalf.
    </template>

    <template #form>
      <!-- Token Name -->
      <v-text-field
        id="name"
        v-model="createApiTokenForm.name"
        type="text"
        label="Name"
        autofocus
      />
      <InputError :message="createApiTokenForm.errors.name" class="text-red"/>

      <!-- Token Permissions -->
      <div v-if="availablePermissions.length > 0">
        <v-checkbox v-for="permission in availablePermissions" :key="permission"
                    :label="permission" :value="permission"
                    v-model="createApiTokenForm.permissions"/>
      </div>
      <pre>{{ createApiTokenForm.permissions }}</pre>
    </template>

    <template #actions>
      <ActionMessage :on="createApiTokenForm.recentlySuccessful" class="me-3">
        Created.
      </ActionMessage>

      <v-btn color="primary" :disabled="createApiTokenForm.processing" type="submit">Create
      </v-btn>

      <!--        <PrimaryButton :class="{ 'opacity-25': createApiTokenForm.processing }"
                             :disabled="createApiTokenForm.processing">
                Create
              </PrimaryButton>-->
    </template>
  </FormSection>

  <div v-if="tokens.length > 0">
    <SectionBorder/>

    <!-- Manage API Tokens -->
    <div class="mt-10 sm:mt-0">
      <ActionSection>
        <template #title>
          Manage API Tokens
        </template>

        <template #description>
          You may delete any of your existing tokens if they are no longer needed.
        </template>

        <!-- API Token List -->
        <template #content>
          <div class="space-y-6">
            <div v-for="token in tokens" :key="token.id"
                 class="d-flex justify-space-between">
              <div class="break-all">
                {{ token.name }}
              </div>

              <div class="d-flex items-center ms-2">
                <div v-if="token.last_used_ago" class="text-sm text-gray-400">
                  Last used {{ token.last_used_ago }}
                </div>

                <v-btn
                  v-if="availablePermissions.length > 0"
                  color="primary"
                  class="me-2"
                  @click="manageApiTokenPermissions(token)"
                >
                  Permissions
                </v-btn>

                <v-btn color="red"
                       @click="confirmApiTokenDeletion(token)">
                  Delete
                </v-btn>
              </div>
            </div>
          </div>
        </template>
      </ActionSection>
    </div>
  </div>

  <!-- Token Value Modal -->
  <DialogModal :show="displayingToken" @close="displayingToken = false">
    <template #title>
      API Token
    </template>

    <template #content>
      <p>
        Please copy your new API token. For your security, it won't be shown again.
      </p>

      <v-card-text v-if="$page.props.jetstream.flash.token"
                   style="word-break: break-all">
        {{ $page.props.jetstream.flash.token }}
      </v-card-text>
    </template>

    <template #footer>
      <v-btn color="secondary" @click="displayingToken = false">Close</v-btn>
    </template>
  </DialogModal>

  <!-- API Token Permissions Modal -->
  <DialogModal :show="managingPermissionsFor != null" @close="managingPermissionsFor = null">
    <template #title>
      API Token Permissions
    </template>

    <template #content>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <v-checkbox v-for="permission in availablePermissions" :key="permission"
                    v-model="updateApiTokenForm.permissions" :label="permission"
                    :value="permission"/>
      </div>
    </template>

    <template #footer>
      <v-btn color="secondary" @click="managingPermissionsFor = null">
        Cancel
      </v-btn>
      <v-btn color="primary"
             :disabled="updateApiTokenForm.processing"
             @click="updateApiToken"
      >
        Save
      </v-btn>
    </template>
  </DialogModal>

  <!-- Delete Token Confirmation Modal -->
  <ConfirmationModal :show="apiTokenBeingDeleted != null" @close="apiTokenBeingDeleted = null">
    <template #title>
      Delete API Token
    </template>

    <template #content>
      Are you sure you would like to delete this API token?
    </template>

    <template #footer>
      <v-btn color="secondary" @click="apiTokenBeingDeleted = null">
        Cancel
      </v-btn>
      <v-btn color="red"
             :disabled="deleteApiTokenForm.processing"
             @click="deleteApiToken"
      >
        Delete
      </v-btn>
    </template>
  </ConfirmationModal>
</template>
