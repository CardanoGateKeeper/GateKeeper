<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import {ref} from "vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const formValid = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    if (!formValid) return
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const rules = {
    required: (v) => !!v || 'Field is required'
}
</script>
<template>
    <Head title="Log in"/>
    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo/>
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
            <v-card-text v-if="status">
                <v-alert type="success">{{ status }}</v-alert>
            </v-card-text>
            <v-card-text>
                <v-text-field label="Email" v-model="form.email" type="email" autocomplete="username" :rules="[rules.required]"/>
                <v-text-field label="Password" v-model="form.password" type="password" autocomplete="current-password" :rules="[rules.required]"/>
                <v-switch v-model="form.remember" color="primary" true-value="1" false-value="0">
                    <template v-slot:label>
                        Remember me
                    </template>
                </v-switch>
            </v-card-text>
            <v-card-actions class="justify-space-between">
                <v-btn :href="route('password.request')" color="secondary" v-if="canResetPassword">
                    Forgot your password?
                </v-btn>
                <v-btn type="submit" :loading="form.processing" :disabled="!formValid" color="primary">
                    Log In
                </v-btn>
            </v-card-actions>
        </v-form>
    </AuthenticationCard>
</template>
