<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {ref} from "vue";

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    if (!formValid) return

    form.post(route('password.email'));
};

const formValid = ref(false)

const rules = {
    required: (v) => !!v || 'Field is required!',
}
</script>

<template>
    <Head title="Forgot Password" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <template #after>
            <p>
                <a :href="route('login')">Return to Login</a>
            </p>
        </template>

<!--        <div class="mb-4 text-sm text-gray-600">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>-->

        <v-form @submit.prevent="submit" v-model="formValid">
            <v-card-text>
                Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
            </v-card-text>
            <template v-if="form.hasErrors && form.errors">
                <v-card-text>
                    <v-alert type="error">
                        <v-alert-title>ERROR</v-alert-title>
                        <p v-for="(error,key) in form.errors" :key="key">
                            {{ error }}
                        </p>
                    </v-alert>
                </v-card-text>
            </template>
            <template v-if="status">
                <v-card-text>
                    <v-alert type="success">
                        {{status}}
                    </v-alert>
                </v-card-text>
            </template>
            <v-card-text>
                <v-text-field label="Email" v-model="form.email" type="email" autocomplete="username"
                              :rules="[rules.required]"/>
            </v-card-text>
            <v-card-actions>
                <v-btn type="submit" color="primary" :loading="form.processing" :disabled="!formValid">
                    Email Password Reset Link
                </v-btn>
            </v-card-actions>
<!--            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Email Password Reset Link
                </PrimaryButton>
            </div>-->
        </v-form>
    </AuthenticationCard>
</template>
