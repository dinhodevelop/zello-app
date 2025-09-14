<script setup lang="ts">
import { Button } from './ui/button';
import { Input } from './ui/input';
import { Label } from './ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from './ui/select';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from './ui/collapsible';
import { Badge } from './ui/badge';
import { Filter, X, ChevronDown } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface FilterOption {
    value: string;
    label: string;
}

interface Props {
    searchTerm: string;
    sortBy: string;
    sortOrder: 'asc' | 'desc';
    statusFilter: string;
    typeFilter?: string;
    categoryFilter?: string;
    showTypeFilter?: boolean;
    showCategoryFilter?: boolean;
    statusOptions: FilterOption[];
    typeOptions?: FilterOption[];
    categoryOptions?: FilterOption[];
    sortOptions: FilterOption[];
}

interface Emits {
    (e: 'update:searchTerm', value: string): void;
    (e: 'update:sortBy', value: string): void;
    (e: 'update:sortOrder', value: 'asc' | 'desc'): void;
    (e: 'update:statusFilter', value: string): void;
    (e: 'update:typeFilter', value: string): void;
    (e: 'update:categoryFilter', value: string): void;
    (e: 'clearFilters'): void;
}

const props = withDefaults(defineProps<Props>(), {
    showTypeFilter: false,
    showCategoryFilter: false,
});

const emit = defineEmits<Emits>();

const isOpen = ref(false);

const activeFiltersCount = computed(() => {
    let count = 0;
    if (props.searchTerm) count++;
    if (props.statusFilter && props.statusFilter !== 'all') count++;
    if (props.typeFilter && props.typeFilter !== 'all') count++;
    if (props.categoryFilter && props.categoryFilter !== 'all') count++;
    return count;
});

const handleClearFilters = () => {
    emit('clearFilters');
};

const getFilterLabel = (value: string, options: FilterOption[]) => {
    const option = options.find(opt => opt.value === value);
    return option?.label || value;
};
</script>

<template>
    <div class="space-y-4">
        <!-- Barra de busca e controles principais -->
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            <div class="flex-1 max-w-md">
                <Input
                    :model-value="searchTerm"
                    @update:model-value="emit('update:searchTerm', $event)"
                    placeholder="Buscar..."
                    class="w-full"
                />
            </div>
            
            <div class="flex items-center space-x-2">
                <!-- Ordenação rápida -->
                <Select :model-value="sortBy" @update:model-value="emit('update:sortBy', $event)">
                    <SelectTrigger class="w-[180px]">
                        <SelectValue placeholder="Ordenar por" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="option in sortOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                
                <Button
                    variant="outline"
                    size="sm"
                    @click="emit('update:sortOrder', sortOrder === 'asc' ? 'desc' : 'asc')"
                >
                    {{ sortOrder === 'asc' ? '↑' : '↓' }}
                </Button>
                
                <!-- Toggle de filtros avançados -->
                <Collapsible v-model:open="isOpen">
                    <CollapsibleTrigger as-child>
                        <Button variant="outline" size="sm" class="relative">
                            <Filter class="mr-2 h-4 w-4" />
                            Filtros
                            <ChevronDown class="ml-2 h-4 w-4 transition-transform" :class="{ 'rotate-180': isOpen }" />
                            <Badge v-if="activeFiltersCount > 0" class="absolute -top-2 -right-2 h-5 w-5 p-0 text-xs">
                                {{ activeFiltersCount }}
                            </Badge>
                        </Button>
                    </CollapsibleTrigger>
                    
                    <CollapsibleContent class="absolute right-0 top-full mt-2 z-10 w-80 bg-background border rounded-lg shadow-lg p-4">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-medium">Filtros Avançados</h3>
                                <Button
                                    v-if="activeFiltersCount > 0"
                                    variant="ghost"
                                    size="sm"
                                    @click="handleClearFilters"
                                >
                                    <X class="mr-2 h-4 w-4" />
                                    Limpar
                                </Button>
                            </div>
                            
                            <!-- Filtro de Status -->
                            <div class="space-y-2">
                                <Label>Status</Label>
                                <Select :model-value="statusFilter" @update:model-value="emit('update:statusFilter', $event)">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Todos os status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">Todos</SelectItem>
                                        <SelectItem v-for="option in statusOptions" :key="option.value" :value="option.value">
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            
                            <!-- Filtro de Tipo -->
                            <div v-if="showTypeFilter && typeOptions" class="space-y-2">
                                <Label>Tipo</Label>
                                <Select :model-value="typeFilter" @update:model-value="emit('update:typeFilter', $event)">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Todos os tipos" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">Todos</SelectItem>
                                        <SelectItem v-for="option in typeOptions" :key="option.value" :value="option.value">
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            
                            <!-- Filtro de Categoria -->
                            <div v-if="showCategoryFilter && categoryOptions" class="space-y-2">
                                <Label>Categoria</Label>
                                <Select :model-value="categoryFilter" @update:model-value="emit('update:categoryFilter', $event)">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Todas as categorias" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">Todas</SelectItem>
                                        <SelectItem v-for="option in categoryOptions" :key="option.value" :value="option.value">
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </CollapsibleContent>
                </Collapsible>
            </div>
        </div>
        
        <!-- Filtros ativos -->
        <div v-if="activeFiltersCount > 0" class="flex flex-wrap gap-2">
            <Badge v-if="searchTerm" variant="secondary" class="flex items-center space-x-1">
                <span>Busca: "{{ searchTerm }}"</span>
                <X class="h-3 w-3 cursor-pointer" @click="emit('update:searchTerm', '')" />
            </Badge>
            
            <Badge v-if="statusFilter && statusFilter !== 'all'" variant="secondary" class="flex items-center space-x-1">
                <span>Status: {{ getFilterLabel(statusFilter, statusOptions) }}</span>
                <X class="h-3 w-3 cursor-pointer" @click="emit('update:statusFilter', 'all')" />
            </Badge>
            
            <Badge v-if="typeFilter && typeFilter !== 'all' && typeOptions" variant="secondary" class="flex items-center space-x-1">
                <span>Tipo: {{ getFilterLabel(typeFilter, typeOptions) }}</span>
                <X class="h-3 w-3 cursor-pointer" @click="emit('update:typeFilter', 'all')" />
            </Badge>
            
            <Badge v-if="categoryFilter && categoryFilter !== 'all' && categoryOptions" variant="secondary" class="flex items-center space-x-1">
                <span>Categoria: {{ getFilterLabel(categoryFilter, categoryOptions) }}</span>
                <X class="h-3 w-3 cursor-pointer" @click="emit('update:categoryFilter', 'all')" />
            </Badge>
        </div>
    </div>
</template>
