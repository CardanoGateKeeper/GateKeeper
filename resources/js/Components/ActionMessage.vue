<script setup>
const props = defineProps({
    on: Boolean,
});
</script>
<template>
    <div>
        <transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
            <div v-show="on" class="text-medium-emphasis text-body-2">
                <slot/>
            </div>
        </transition>
    </div>
</template>
