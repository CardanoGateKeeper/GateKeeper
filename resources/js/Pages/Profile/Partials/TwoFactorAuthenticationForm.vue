<script setup>
import {computed, ref, watch} from 'vue';
import {router, useForm, usePage} from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => !enabling.value && page.props.auth.user?.two_factor_enabled,
);

watch(twoFactorEnabled, () => {
    if (!twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
}

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios
        .post(route('two-factor.recovery-codes'))
        .then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};
</script>
<style>
.text-mono {
    font-family: monospace;
}
</style>
<template>
    <ActionSection>
        <template #title>
            Two Factor Authentication
        </template>
        <template #description>
            Add additional security to your account using two factor authentication.
        </template>
        <template #content>
            <h3>
                <template v-if="twoFactorEnabled && ! confirming">
                    You have enabled two factor authentication.
                </template>
                <template v-else-if="twoFactorEnabled && confirming">
                    Finish enabling two factor authentication.
                </template>
                <template v-else>
                    You have not enabled two factor authentication.
                </template>
            </h3>
            <p class="my-4">
                When two factor authentication is enabled, you will be prompted for a secure, random token during
                authentication. You may retrieve this token from your phone's Google Authenticator application.
            </p>
            <template v-if="twoFactorEnabled">
                <template v-if="qrCode">
                    <p v-if="confirming">
                        To finish enabling two factor authentication, scan the following QR code using your phone's
                        authenticator application or enter the setup key and provide the generated OTP code.
                    </p>
                    <p v-else>
                        Two factor authentication is now enabled. Scan the following QR code using your phone's
                        authenticator application or enter the setup key.
                    </p>
                    <v-row class="my-4">
                        <v-col cols="auto">
                            <div class="pa-2 bg-white" v-html="qrCode"/>
                        </v-col>
                        <v-col cols="">
                            <v-text-field label="Setup Key" readonly v-model="setupKey"/>
                            <v-text-field label="Code" v-model="confirmationForm.code" inputmode="numeric" type="text"
                                          autocomplete="one-time-code" @keyup.enter="confirmTwoFactorAuthentication"/>
                            <v-alert v-if="$page.props.errors.confirmTwoFactorAuthentication" type="error" class="my-4">
                                <v-alert-title>ERROR</v-alert-title>
                                <p v-for="(error,i) in $page.props.errors.confirmTwoFactorAuthentication" :key="i">
                                    {{ error }}
                                </p>
                            </v-alert>
                            <p>{{ confirmationForm }}</p>
<!--                            <p>{{ confirmationForm }}</p>-->

                        </v-col>
                    </v-row>
                    <!--                    <div class="mt-4 p-2 inline-block bg-white" v-html="qrCode"/>-->
                    <!--                    <div v-if="setupKey" class="mt-4 max-w-xl text-sm text-gray-600">
                                            <p class="font-semibold">
                                                Setup Key: <span v-html="setupKey"></span>
                                            </p>
                                        </div>-->
                    <!--                    <div v-if="confirming" class="mt-4">
                                            <InputLabel for="code" value="Code"/>
                                            <TextInput id="code" v-model="confirmationForm.code" type="text" name="code"
                                                       class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                                                       @keyup.enter="confirmTwoFactorAuthentication"/>
                                            <InputError :message="confirmationForm.errors.code" class="mt-2"/>
                                        </div>-->
                </template>
                <template v-if="recoveryCodes.length > 0 && ! confirming">
                    <p>
                        Store these recovery codes in a secure password manager. They can be used to recover access to
                        your account if your two factor authentication device is lost.
                    </p>
                    <v-row class="text-mono my-4">
                        <v-col cols="auto" v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </v-col>
                    </v-row>
                    <!--                    <code>ABC123</code>
                                        <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                                            <div v-for="code in recoveryCodes" :key="code">
                                                {{ code }}
                                            </div>
                                        </div>-->
                </template>
            </template>
            <template v-if="! twoFactorEnabled">
                <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
                    <v-btn :loading="enabling" :disabled="enabling" color="primary">Enable</v-btn>
                    <!--                    <PrimaryButton type="button" :class="{ 'opacity-25': enabling }" :disabled="enabling">
                                            Enable
                                        </PrimaryButton>-->
                </ConfirmsPassword>
            </template>
            <template v-else>
                <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
                    <v-btn class="me-2" v-if="confirming" :loading="enabling" :disabled="enabling" color="primary">
                        Confirm
                    </v-btn>
                    <!--                    <PrimaryButton v-if="confirming" type="button" class="me-3" :class="{ 'opacity-25': enabling }"
                                                       :disabled="enabling">
                                            Confirm
                                        </PrimaryButton>-->
                </ConfirmsPassword>
                <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                    <v-btn class="me-2" v-if="recoveryCodes.length > 0 && !confirming" color="secondary">
                        Regenerate Recovery Codes
                    </v-btn>
                    <!--                    <SecondaryButton v-if="recoveryCodes.length > 0 && ! confirming" class="me-3">
                                            Regenerate Recovery Codes
                                        </SecondaryButton>-->
                </ConfirmsPassword>
                <ConfirmsPassword @confirmed="showRecoveryCodes">
                    <v-btn class="me-2" v-if="recoveryCodes.length > 0 && !confirming" color="secondary">
                        Show Recovery Codes
                    </v-btn>
                    <!--                    <SecondaryButton v-if="recoveryCodes.length === 0 && ! confirming" class="me-3">
                                            Show Recovery Codes
                                        </SecondaryButton>-->
                </ConfirmsPassword>
                <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                    <v-btn class="me-2" v-if="confirming" :loading="disabling" :disabled="disabling">
                        Cancel
                    </v-btn>
                    <!--                    <SecondaryButton v-if="confirming" :class="{ 'opacity-25': disabling }" :disabled="disabling">
                                            Cancel
                                        </SecondaryButton>-->
                </ConfirmsPassword>
                <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                    <v-btn class="me-2" color="error" v-if="!confirming" :loading="disabling" :disabled="disabling">
                        Disable
                    </v-btn>
                    <!--                    <DangerButton v-if="! confirming" :class="{ 'opacity-25': disabling }" :disabled="disabling">
                                            Disable
                                        </DangerButton>-->
                </ConfirmsPassword>
            </template>
        </template>
    </ActionSection>
</template>
