<script setup>
import SectionTitle from './SectionTitle.vue';</script>
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
            <slot name="content"/>
        </v-col>
    </v-row>
    <!--    <div class="md:grid md:grid-cols-3 md:gap-6">-->
    <!--        <SectionTitle>-->
    <!--            <template #title>-->
    <!--                <slot name="title" />-->
    <!--            </template>-->
    <!--            <template #description>-->
    <!--                <slot name="description" />-->
    <!--            </template>-->
    <!--        </SectionTitle>-->
    <!--        <div class="mt-5 md:mt-0 md:col-span-2">-->
    <!--            <div class="px-4 py-5 sm:p-6 bg-white shadow sm:rounded-lg">-->
    <!--                <slot name="content" />-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
</template>
