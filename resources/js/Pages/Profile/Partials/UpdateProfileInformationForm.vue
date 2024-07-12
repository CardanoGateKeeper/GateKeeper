<script setup>
import {ref} from 'vue';
import {Link, router, useForm} from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>
<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Profile Information
        </template>
        <template #description>
            Update your account's profile information and email address.
        </template>
        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos">
                <div class="mb-4">
                    <input id="photo" ref="photoInput" type="file" class="d-none" accept="image/*"
                           @change="updatePhotoPreview">
                    <v-img v-show="!photoPreview" :src="user.profile_photo_url" :alt="user.name" contain
                           max-width="50%"/>
                    <v-img v-show="photoPreview" :src="photoPreview" contain width="50%"/>
                </div>
                <v-row class="mb-4">
                    <v-col cols="auto">
                        <v-btn @click.prevent="selectNewPhoto">Select a New Photo</v-btn>
                    </v-col>
                    <v-col cols="auto">
                        <v-btn @click.prevent="deletePhoto">Remove Photo</v-btn>
                    </v-col>
                </v-row>
                <InputError :message="form.errors.photo" class="mt-2"/>
            </div>
            <!-- Name -->
            <p>
                <v-text-field label="Name" v-model="form.name" type="text" autocomplete="name"/>
            </p>
            <v-text-field label="Email" v-model="form.email" type="email" autocomplete="username"/>
            <template v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                <v-alert type="warning">
                    <p>
                        Your email address is unverified.
                        <Link :href="route('verification.send')" method="post" as="button"
                              class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                              @click.prevent="sendEmailVerification">
                            Click here to re-send the verification email.
                        </Link>
                    </p>
                    <p v-show="verificationLinkSent">
                        <strong>A new verification link has been sent to your email address.</strong>
                    </p>
                </v-alert>
            </template>
            <!--            <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="name" value="Name"/>
                            <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required
                                       autocomplete="name"/>
                            <InputError :message="form.errors.name" class="mt-2"/>
                        </div>-->
            <!-- Email -->
            <!--            <div class="col-span-6 sm:col-span-4">
            &lt;!&ndash;                <InputLabel for="email" value="Email"/>
                            <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
                                       autocomplete="username"/>
                            <InputError :message="form.errors.email" class="mt-2"/>&ndash;&gt;
                            <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                                <p class="text-sm mt-2">
                                    Your email address is unverified.
                                    <Link :href="route('verification.send')" method="post" as="button"
                                          class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                          @click.prevent="sendEmailVerification">
                                        Click here to re-send the verification email.
                                    </Link>
                                </p>
                                <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
                                    A new verification link has been sent to your email address.
                                </div>
                            </div>
                        </div>-->
        </template>
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>
            <v-btn type="submit" color="primary" :loading="form.processing" :disabled="form.processing">Save</v-btn>
            <!--            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save
                        </PrimaryButton>-->
        </template>
    </FormSection>
</template>
