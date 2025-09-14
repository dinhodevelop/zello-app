<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import ViewModeToggle from '../../components/ViewModeToggle.vue';
import ListFilters from '../../components/ListFilters.vue';
import DespesaCard from '../../components/DespesaCard.vue';
import DespesaListItem from '../../components/DespesaListItem.vue';
import { useViewMode } from '../../composables/useViewMode';
import { Plus } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Despesa {
    id: number;
    descricao: string;
    valor: string;
    tipo: 'fixa' | 'variavel';
    categoria: string;
    status: 'pago' | 'pendente' | 'vencido';
    data_pagamento: string | null;
    data_vencimento: string;
    recorrente: boolean;
    observacoes: string | null;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    despesas: Despesa[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Despesas',
        href: '/despesas',
    },
];

// View mode
const { viewMode, setViewMode } = useViewMode('despesas-view-mode');

// Filters
const searchTerm = ref('');
const sortBy = ref('created_at');
const sortOrder = ref<'asc' | 'desc'>('desc');
const statusFilter = ref('all');
const typeFilter = ref('all');
const categoryFilter = ref('all');

// Filter options
const statusOptions = [
    { value: 'pago', label: 'Pago' },
    { value: 'pendente', label: 'Pendente' },
    { value: 'vencido', label: 'Vencido' },
];

const typeOptions = [
    { value: 'fixa', label: 'Fixa' },
    { value: 'variavel', label: 'Variável' },
];

// Get unique categories from despesas
const categoryOptions = computed(() => {
    const categories = [...new Set(props.despesas.map(d => d.categoria))];
    return categories.map(cat => ({ value: cat, label: cat }));
});

const sortOptions = [
    { value: 'created_at', label: 'Data de criação' },
    { value: 'valor', label: 'Valor' },
    { value: 'descricao', label: 'Descrição' },
    { value: 'data_vencimento', label: 'Vencimento' },
    { value: 'categoria', label: 'Categoria' },
];

// Filtered and sorted despesas
const filteredDespesas = computed(() => {
    let filtered = [...props.despesas];

    // Apply search filter
    if (searchTerm.value) {
        const search = searchTerm.value.toLowerCase();
        filtered = filtered.filter(despesa =>
            despesa.descricao.toLowerCase().includes(search) ||
            despesa.categoria.toLowerCase().includes(search)
        );
    }

    // Apply status filter
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(despesa => despesa.status === statusFilter.value);
    }

    // Apply type filter
    if (typeFilter.value !== 'all') {
        filtered = filtered.filter(despesa => despesa.tipo === typeFilter.value);
    }

    // Apply category filter
    if (categoryFilter.value !== 'all') {
        filtered = filtered.filter(despesa => despesa.categoria === categoryFilter.value);
    }

    // Apply sorting
    filtered.sort((a, b) => {
        let aValue: any = a[sortBy.value as keyof Despesa];
        let bValue: any = b[sortBy.value as keyof Despesa];

        if (sortBy.value === 'valor') {
            aValue = parseFloat(a.valor);
            bValue = parseFloat(b.valor);
        }

        if (aValue < bValue) return sortOrder.value === 'asc' ? -1 : 1;
        if (aValue > bValue) return sortOrder.value === 'asc' ? 1 : -1;
        return 0;
    });

    return filtered;
});

const clearFilters = () => {
    searchTerm.value = '';
    statusFilter.value = 'all';
    typeFilter.value = 'all';
    categoryFilter.value = 'all';
};

const deleteDespesa = (id: number) => {
    if (confirm('Tem certeza que deseja excluir esta despesa?')) {
        router.delete(`/despesas/${id}`);
    }
};
</script>

<template>
    <Head title="Despesas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Despesas</h1>
                    <p class="text-muted-foreground">
                        Gerencie suas despesas e contas ({{ filteredDespesas.length }} {{ filteredDespesas.length === 1 ? 'item' : 'itens' }})
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <ViewModeToggle v-model="viewMode" @update:model-value="setViewMode" />
                    <Link href="/despesas/create">
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Nova Despesa
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <ListFilters
                v-model:search-term="searchTerm"
                v-model:sort-by="sortBy"
                v-model:sort-order="sortOrder"
                v-model:status-filter="statusFilter"
                v-model:type-filter="typeFilter"
                v-model:category-filter="categoryFilter"
                :status-options="statusOptions"
                :type-options="typeOptions"
                :category-options="categoryOptions"
                :sort-options="sortOptions"
                :show-type-filter="true"
                :show-category-filter="true"
                @clear-filters="clearFilters"
            />

            <Separator />

            <!-- Lista de Despesas -->
            <div v-if="despesas.length === 0" class="flex flex-col items-center justify-center py-12">
                <div class="text-center">
                    <h3 class="text-lg font-semibold">Nenhuma despesa encontrada</h3>
                    <p class="text-muted-foreground mb-4">
                        Comece adicionando sua primeira despesa
                    </p>
                    <Link href="/despesas/create">
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Adicionar Despesa
                        </Button>
                    </Link>
                </div>
            </div>

            <div v-else-if="filteredDespesas.length === 0" class="flex flex-col items-center justify-center py-12">
                <div class="text-center">
                    <h3 class="text-lg font-semibold">Nenhuma despesa encontrada</h3>
                    <p class="text-muted-foreground mb-4">
                        Tente ajustar os filtros de busca
                    </p>
                    <Button variant="outline" @click="clearFilters">
                        Limpar Filtros
                    </Button>
                </div>
            </div>

            <!-- Cards View -->
            <div v-else-if="viewMode === 'cards'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <DespesaCard 
                    v-for="despesa in filteredDespesas" 
                    :key="despesa.id" 
                    :despesa="despesa"
                    @delete="deleteDespesa"
                />
            </div>

            <!-- List View -->
            <div v-else class="space-y-2">
                <DespesaListItem 
                    v-for="despesa in filteredDespesas" 
                    :key="despesa.id" 
                    :despesa="despesa"
                    @delete="deleteDespesa"
                />
            </div>
        </div>
    </AppLayout>
</template>
