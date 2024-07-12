<script setup>
import {nextTick, reactive, ref} from 'vue';
import DialogModal from './DialogModal.vue';

const emit = defineEmits(['confirmed']);

defineProps({
    title: {
        type: String,
        default: 'Confirm Password',
    },
    content: {
        type: String,
        default: 'For your security, please confirm your password to continue.',
    },
    button: {
        type: String,
        default: 'Confirm',
    },
});

const form = reactive({
    password: '',
    error: '',
    processing: false,
    show: false
});

const startConfirmingPassword = () => {
    axios.get(route('password.confirmation')).then(response => {
        if (response.data.confirmed) {
            emit('confirmed');
        } else {
            form.show = true;
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    axios.post(route('password.confirm'), {
        password: form.password,
    }).then(() => {
        form.processing = false;

        closeModal();
        nextTick().then(() => emit('confirmed'));

    }).catch(error => {
        form.processing = false;
        form.error = error.response.data.errors.password[0];
    });
};

const closeModal = () => {
    form.show = false;
    form.password = '';
    form.error = '';
};
</script>
<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot/>
        </span>

        <DialogModal :show="form.show" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}

                <v-text-field class="mt-4" type="password" v-model="form.password" label="Password"
                              autocomplete="current-password" @keyup.enter="confirmPassword"/>
            </template>

            <template #footer>
                <v-btn @click="closeModal">Cancel</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="confirmPassword" :loading="form.processing" :disabled="form.processing">
                    {{ button }}
                </v-btn>
            </template>
        </DialogModal>
    </span>
</template>
