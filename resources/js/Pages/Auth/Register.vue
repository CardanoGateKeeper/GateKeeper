<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import {ref} from "vue";


const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    if (!formValid) return

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const formValid = ref(false)

const rules = {
    required: (v) => !!v || 'Field is required!',
    passMatch: (v) => (v === form.password) || 'Passwords do not match!'
}
</script>
<template>
    <Head title="Register"/>
    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo/>
        </template>
        <template v-if="form.hasErrors">
            <v-card-text v-if="form.errors">
                <v-alert type="error">
                    <v-alert-title>Registration Failed</v-alert-title>
                    <p v-for="(error,key) in form.errors" :key="key">
                        {{ error }}
                    </p>
                </v-alert>
            </v-card-text>
        </template>
        <v-form @submit.prevent="submit" v-model="formValid">
            <v-card-text>
                <v-text-field label="Name" v-model="form.name" type="text" autocomplete="name"
                              :rules="[rules.required]"/>
                <v-text-field label="Email" v-model="form.email" type="email" autocomplete="username"
                              :rules="[rules.required]"/>
                <v-text-field label="Password" v-model="form.password" type="password" autocomplete="new-password"
                              :rules="[rules.required]"/>
                <v-text-field label="Confirm Password" v-model="form.password_confirmation" type="password"
                              autocomplete="new-password" :rules="[rules.required, rules.passMatch]"/>
                <v-switch v-model="form.terms" color="primary" true-value="1" false-value="0" :rules="[rules.required]"
                          v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature">
                    <template v-slot:label>
                        I agree to the
                        <a target="_blank" :href="route('terms.show')">
                            Terms of Service
                        </a>
                        and
                        <a target="_blank" :href="route('policy.show')">
                            Privacy Policy
                        </a>
                    </template>
                </v-switch>
            </v-card-text>
            <v-card-actions class="justify-space-between">
                <v-btn :href="route('login')" color="secondary">Already Registered?</v-btn>
                <v-btn type="submit" color="primary" :loading="form.processing" :disabled="!formValid">Register
                </v-btn>
            </v-card-actions>
        </v-form>
    </AuthenticationCard>
</template>
