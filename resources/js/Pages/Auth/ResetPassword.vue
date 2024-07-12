<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {ref} from "vue";

const props = defineProps({
    email: String,
    token: String,
});

const formValid = ref(false)

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    if (!formValid) return
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const rules = {
    required: (v) => !!v || 'Field is required!',
    passMatch: (v) => (v === form.password) || 'Passwords do not match!'
}
</script>

<template>
    <Head title="Reset Password" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <v-form @submit.prevent="submit" v-model="formValid">
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
            <v-card-text>
                <v-text-field label="Email" v-model="form.email" type="email" autocomplete="username"
                              :rules="[rules.required]"/>
                <v-text-field label="Password" v-model="form.password" type="password" autocomplete="new-password"
                              :rules="[rules.required]"/>
                <v-text-field label="Confirm Password" v-model="form.password_confirmation" type="password"
                              autocomplete="new-password" :rules="[rules.required, rules.passMatch]"/>
            </v-card-text>
            <v-card-actions>
                <v-btn type="submit" color="primary" :loading="form.processing" :disabled="!formValid">
                    Reset Password
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

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Reset Password
                </PrimaryButton>
            </div>-->
        </v-form>
    </AuthenticationCard>
</template>
