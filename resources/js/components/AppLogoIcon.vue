<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import logosem from '../assets/logosem.png';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
<img :src="logosem" :class="className" v-bind="$attrs" />
</template>
