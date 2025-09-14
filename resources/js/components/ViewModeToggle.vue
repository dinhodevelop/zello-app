<script setup lang="ts">
import { Button } from './ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from './ui/tooltip';
import { type ViewMode } from '../composables/useViewMode';
import { LayoutGrid, List } from 'lucide-vue-next';

interface Props {
    modelValue: ViewMode;
    showLabels?: boolean;
}

interface Emits {
    (e: 'update:modelValue', value: ViewMode): void;
}

// Declarando props com defaults sem criar a variável props
withDefaults(defineProps<Props>(), {
    showLabels: false,
});

const emit = defineEmits<Emits>();

const modes = [
    { value: 'list' as const, icon: List, label: 'Lista' },
    { value: 'cards' as const, icon: LayoutGrid, label: 'Cards' },
];

const handleModeChange = (mode: ViewMode) => {
    emit('update:modelValue', mode);
};
</script>

<template>
  <div class="flex items-center space-x-1 rounded-lg border p-1">
    <template v-for="mode in modes" :key="mode.value">
      <TooltipProvider v-if="!showLabels" :delay-duration="0">
        <Tooltip>
          <TooltipTrigger as-child>
            <Button
              :variant="modelValue === mode.value ? 'default' : 'ghost'"
              size="sm"
              @click="handleModeChange(mode.value)"
              class="h-8 w-8 p-0"
            >
              <component :is="mode.icon" class="h-4 w-4" />
              <span class="sr-only">{{ mode.label }}</span>
            </Button>
          </TooltipTrigger>
          <TooltipContent>
            <p>Visualizar como {{ mode.label.toLowerCase() }}</p>
          </TooltipContent>
        </Tooltip>
      </TooltipProvider>

      <Button
        v-else
        :variant="modelValue === mode.value ? 'default' : 'ghost'"
        size="sm"
        @click="handleModeChange(mode.value)"
        class="flex items-center space-x-2"
      >
        <component :is="mode.icon" class="h-4 w-4" />
        <span>{{ mode.label }}</span>
      </Button>
    </template>
  </div>
</template>
