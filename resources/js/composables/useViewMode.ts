import { ref, onMounted } from 'vue';

export type ViewMode = 'cards' | 'list';

export function useViewMode(storageKey: string = 'view-mode') {
    const viewMode = ref<ViewMode>('cards');

    onMounted(() => {
        const saved = localStorage.getItem(storageKey) as ViewMode | null;
        if (saved && ['cards', 'list'].includes(saved)) {
            viewMode.value = saved;
        }
    });

    const setViewMode = (mode: ViewMode) => {
        viewMode.value = mode;
        localStorage.setItem(storageKey, mode);
    };

    return {
        viewMode,
        setViewMode,
    };
}
