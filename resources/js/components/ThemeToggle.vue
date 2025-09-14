<script setup lang="ts">
import { useAppearance } from '../composables/useAppearance';
import { Button } from './ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from './ui/dropdown-menu';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from './ui/tooltip';
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    variant?: 'icon' | 'full' | 'dropdown';
    showTooltip?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'icon',
    showTooltip: true,
});

const { appearance, updateAppearance } = useAppearance();

const currentIcon = computed(() => {
    switch (appearance.value) {
        case 'light':
            return Sun;
        case 'dark':
            return Moon;
        case 'system':
        default:
            return Monitor;
    }
});

const currentLabel = computed(() => {
    switch (appearance.value) {
        case 'light':
            return 'Modo Claro';
        case 'dark':
            return 'Modo Escuro';
        case 'system':
        default:
            return 'Sistema';
    }
});

const themes = [
    { value: 'light', icon: Sun, label: 'Claro' },
    { value: 'dark', icon: Moon, label: 'Escuro' },
    { value: 'system', icon: Monitor, label: 'Sistema' },
] as const;
</script>

<template>
    <!-- Variante com dropdown -->
    <DropdownMenu v-if="variant === 'dropdown'">
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="h-9 w-9">
                <component :is="currentIcon" class="h-4 w-4" />
                <span class="sr-only">Alternar tema</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem
                v-for="theme in themes"
                :key="theme.value"
                @click="updateAppearance(theme.value)"
                class="cursor-pointer"
            >
                <component :is="theme.icon" class="mr-2 h-4 w-4" />
                {{ theme.label }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>

    <!-- Variante completa com texto -->
    <div v-else-if="variant === 'full'" class="flex items-center space-x-2">
        <Button
            v-for="theme in themes"
            :key="theme.value"
            @click="updateAppearance(theme.value)"
            :variant="appearance === theme.value ? 'default' : 'outline'"
            size="sm"
            class="flex items-center space-x-2"
        >
            <component :is="theme.icon" class="h-4 w-4" />
            <span>{{ theme.label }}</span>
        </Button>
    </div>

    <!-- Variante apenas ícone com tooltip -->
    <TooltipProvider v-else-if="showTooltip" :delay-duration="0">
        <Tooltip>
            <TooltipTrigger as-child>
                <Button
                    variant="ghost"
                    size="icon"
                    @click="updateAppearance(appearance === 'light' ? 'dark' : appearance === 'dark' ? 'system' : 'light')"
                    class="h-9 w-9"
                >
                    <component :is="currentIcon" class="h-4 w-4" />
                    <span class="sr-only">Alternar tema</span>
                </Button>
            </TooltipTrigger>
            <TooltipContent>
                <p>{{ currentLabel }}</p>
            </TooltipContent>
        </Tooltip>
    </TooltipProvider>

    <!-- Variante apenas ícone sem tooltip -->
    <Button
        v-else
        variant="ghost"
        size="icon"
        @click="updateAppearance(appearance === 'light' ? 'dark' : appearance === 'dark' ? 'system' : 'light')"
        class="h-9 w-9"
    >
        <component :is="currentIcon" class="h-4 w-4" />
        <span class="sr-only">Alternar tema</span>
    </Button>
</template>
