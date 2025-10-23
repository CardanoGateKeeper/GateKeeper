<script setup>
import {computed, onMounted, ref} from "vue";

const emit = defineEmits(['close']);

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '512',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit('close');
};

const show = computed(() => {
    console.log("in dialog modal...", props.show);
    return props.show
})

// const show = ref(props.show);

onMounted(() => {
    console.log(props);
})
</script>
<template>
    <v-dialog v-model="show" width="auto" :max-width="props.maxWidth" :persistent="!props.closeable" scrollable>
        <v-card>
            <v-card-title class="d-flex align-center">
                <slot name="title"/>
                <v-spacer></v-spacer>
                <v-btn icon="mdi-close" elevation="0" @click="close">
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <slot name="content"/>
            </v-card-text>
            <v-card-actions>
                <slot name="footer"/>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <!--    <Modal
            :show="show"
            :max-width="maxWidth"
            :closeable="closeable"
            @close="close"
        >
            <div class="px-6 py-4">
                <div class="text-lg font-medium text-gray-900">
                    <slot name="title" />
                </div>

                <div class="mt-4 text-sm text-gray-600">
                    <slot name="content" />
                </div>
            </div>

            <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-end">
                <slot name="footer" />
            </div>
        </Modal>-->
</template>
