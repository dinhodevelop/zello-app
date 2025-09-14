<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import ViewModeToggle from '@/components/ViewModeToggle.vue';
import ListFilters from '@/components/ListFilters.vue';
import ReceitaCard from '@/components/ReceitaCard.vue';
import ReceitaListItem from '@/components/ReceitaListItem.vue';
import { useViewMode } from '@/composables/useViewMode';
import { Plus } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Receita {
    id: number;
    descricao: string;
    valor: string;
    tipo: 'salario' | 'freelance' | 'rendimento' | 'outros';
    frequencia: 'mensal' | 'semanal' | 'unica';
    status: 'recebido' | 'pendente';
    data_recebimento: string | null;
    data_vencimento: string | null;
    observacoes: string | null;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    receitas: Receita[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Receitas',
        href: '/receitas',
    },
];

// View mode
const { viewMode, setViewMode } = useViewMode('receitas-view-mode');

// Filters
const searchTerm = ref('');
const sortBy = ref('created_at');
const sortOrder = ref<'asc' | 'desc'>('desc');
const statusFilter = ref('all');
const typeFilter = ref('all');

// Filter options
const statusOptions = [
    { value: 'recebido', label: 'Recebido' },
    { value: 'pendente', label: 'Pendente' },
];

const typeOptions = [
    { value: 'salario', label: 'Salário' },
    { value: 'freelance', label: 'Freelance' },
    { value: 'rendimento', label: 'Rendimento' },
    { value: 'outros', label: 'Outros' },
];

const sortOptions = [
    { value: 'created_at', label: 'Data de criação' },
    { value: 'valor', label: 'Valor' },
    { value: 'descricao', label: 'Descrição' },
    { value: 'data_vencimento', label: 'Vencimento' },
];

// Filtered and sorted receitas
const filteredReceitas = computed(() => {
    let filtered = [...props.receitas];

    // Apply search filter
    if (searchTerm.value) {
        const search = searchTerm.value.toLowerCase();
        filtered = filtered.filter(receita =>
            receita.descricao.toLowerCase().includes(search) ||
            receita.tipo.toLowerCase().includes(search)
        );
    }

    // Apply status filter
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(receita => receita.status === statusFilter.value);
    }

    // Apply type filter
    if (typeFilter.value !== 'all') {
        filtered = filtered.filter(receita => receita.tipo === typeFilter.value);
    }

    // Apply sorting
    filtered.sort((a, b) => {
        let aValue: any = a[sortBy.value as keyof Receita];
        let bValue: any = b[sortBy.value as keyof Receita];

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
};

const deleteReceita = (id: number) => {
    if (confirm('Tem certeza que deseja excluir esta receita?')) {
        router.delete(`/receitas/${id}`);
    }
};
</script>

<template>
    <Head title="Receitas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Receitas</h1>
                    <p class="text-muted-foreground">
                        Gerencie suas receitas e rendimentos ({{ filteredReceitas.length }} {{ filteredReceitas.length === 1 ? 'item' : 'itens' }})
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <ViewModeToggle v-model="viewMode" @update:model-value="setViewMode" />
                    <Link href="/receitas/create">
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Nova Receita
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
                :status-options="statusOptions"
                :type-options="typeOptions"
                :sort-options="sortOptions"
                :show-type-filter="true"
                @clear-filters="clearFilters"
            />

            <Separator />

            <!-- Lista de Receitas -->
            <div v-if="receitas.length === 0" class="flex flex-col items-center justify-center py-12">
                <div class="text-center">
                    <h3 class="text-lg font-semibold">Nenhuma receita encontrada</h3>
                    <p class="text-muted-foreground mb-4">
                        Comece adicionando sua primeira receita
                    </p>
                    <Link href="/receitas/create">
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Adicionar Receita
                        </Button>
                    </Link>
                </div>
            </div>

            <div v-else-if="filteredReceitas.length === 0" class="flex flex-col items-center justify-center py-12">
                <div class="text-center">
                    <h3 class="text-lg font-semibold">Nenhuma receita encontrada</h3>
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
                <ReceitaCard 
                    v-for="receita in filteredReceitas" 
                    :key="receita.id" 
                    :receita="receita"
                    @delete="deleteReceita"
                />
            </div>

            <!-- List View -->
            <div v-else class="space-y-2">
                <ReceitaListItem 
                    v-for="receita in filteredReceitas" 
                    :key="receita.id" 
                    :receita="receita"
                    @delete="deleteReceita"
                />
            </div>
        </div>
    </AppLayout>
</template>
