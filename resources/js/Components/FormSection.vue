<script setup>
import {computed, ref, useSlots} from 'vue';
import SectionTitle from './SectionTitle.vue';

defineEmits(['submitted']);

const hasActions = computed(() => !!useSlots().actions);

</script>
<template>
    <v-row>
        <v-col cols="12" md="4">
            <SectionTitle>
                <template #title>
                    <slot name="title"/>
                </template>
                <template #description>
                    <slot name="description"/>
                </template>
            </SectionTitle>
        </v-col>
        <v-col cols="12" md="8">
            <v-form @submit.prevent="$emit('submitted')">
                <v-card>
                    <v-card-text>
                        <slot name="form"/>
                    </v-card-text>
                    <v-card-actions class="justify-end" v-if="hasActions">
                        <slot name="actions"/>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-col>
    </v-row>
    <!--    <div class="md:grid md:grid-cols-3 md:gap-6">
            <SectionTitle>
                <template #title>
                    <slot name="title"/>
                </template>
                <template #description>
                    <slot name="description"/>
                </template>
            </SectionTitle>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form @submit.prevent="$emit('submitted')">
                    <div class="px-4 py-5 bg-white sm:p-6 shadow"
                         :class="hasActions ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md'">
                        <div class="grid grid-cols-6 gap-6">
                            <slot name="form"/>
                        </div>
                    </div>
                    <div v-if="hasActions"
                         class="flex items-center justify-end px-4 py-3 bg-gray-50 text-end sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        <slot name="actions"/>
                    </div>
                </form>
            </div>
        </div>-->
</template>
