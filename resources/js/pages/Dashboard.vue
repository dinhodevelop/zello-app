<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '../routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Plus, CreditCard, Receipt, TrendingUp, TrendingDown, Filter, Calendar, Eye } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Resumo {
    total_receitas: number;
    total_despesas: number;
    saldo: number;
    receitas_recebidas: number;
    receitas_pendentes: number;
    despesas_pagas: number;
    despesas_pendentes: number;
    despesas_vencidas: number;
}

interface Receita {
    id: number;
    descricao: string;
    valor: string;
    tipo: string;
    status: string;
    created_at: string;
}

interface Despesa {
    id: number;
    descricao: string;
    valor: string;
    categoria: string;
    status: string;
    data_vencimento: string;
}

interface Filtros {
    periodo: string;
    data_inicio: string | null;
    data_fim: string | null;
    periodo_label: string;
}

const props = defineProps<{
    filtros: Filtros;
    resumo: Resumo;
    listas: {
        receitas_recentes: Receita[];
        despesas_pendentes: Despesa[];
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const showFilters = ref(false);
const selectedPeriodo = ref(props.filtros.periodo);
const dataInicio = ref(props.filtros.data_inicio || '');
const dataFim = ref(props.filtros.data_fim || '');

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const getSaldoColor = computed(() => {
    if (props.resumo.saldo > 0) return 'text-green-600';
    if (props.resumo.saldo < 0) return 'text-red-600';
    return 'text-gray-600';
});

const applyFilters = () => {
    const params: any = { periodo: selectedPeriodo.value };
    
    if (selectedPeriodo.value === 'personalizado') {
        params.data_inicio = dataInicio.value;
        params.data_fim = dataFim.value;
    }
    
    router.get('/dashboard', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    selectedPeriodo.value = 'mes_atual';
    dataInicio.value = '';
    dataFim.value = '';
    applyFilters();
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- Header com Filtros -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                    <p class="text-muted-foreground">
                        {{ filtros.periodo_label }}
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <Button variant="outline" @click="showFilters = !showFilters">
                        <Filter class="mr-2 h-4 w-4" />
                        Filtros
                    </Button>
                    <Link href="/receitas/create">
                        <Button variant="outline">
                            <Plus class="mr-2 h-4 w-4" />
                            Nova Receita
                        </Button>
                    </Link>
                    <Link href="/despesas/create">
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Nova Despesa
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Painel de Filtros -->
            <Card v-if="showFilters">
                <CardHeader>
                    <CardTitle class="flex items-center">
                        <Calendar class="mr-2 h-4 w-4" />
                        Filtros de Período
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <Label for="periodo">Período</Label>
                            <select
                                id="periodo"
                                v-model="selectedPeriodo"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="mes_atual">Mês Atual</option>
                                <option value="mes_anterior">Mês Anterior</option>
                                <option value="ano_atual">Ano Atual</option>
                                <option value="ultimos_30_dias">Últimos 30 dias</option>
                                <option value="ultimos_90_dias">Últimos 90 dias</option>
                                <option value="personalizado">Personalizado</option>
                            </select>
                        </div>
                        
                        <div v-if="selectedPeriodo === 'personalizado'">
                            <Label for="data_inicio">Data Início</Label>
                            <Input
                                id="data_inicio"
                                v-model="dataInicio"
                                type="date"
                            />
                        </div>
                        
                        <div v-if="selectedPeriodo === 'personalizado'">
                            <Label for="data_fim">Data Fim</Label>
                            <Input
                                id="data_fim"
                                v-model="dataFim"
                                type="date"
                            />
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <Button @click="applyFilters">
                                Aplicar
                            </Button>
                            <Button variant="outline" @click="clearFilters">
                                Limpar
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Cards de Resumo -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Receitas -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Receitas</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(resumo.total_receitas) }}</div>
                        <p class="text-xs text-muted-foreground">
                            Recebidas: {{ formatCurrency(resumo.receitas_recebidas) }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Pendentes: {{ formatCurrency(resumo.receitas_pendentes) }}
                        </p>
                        <Link href="/receitas" class="mt-3 block">
                            <Button variant="outline" size="sm" class="w-full">
                                <Receipt class="mr-2 h-4 w-4" />
                                Ver Receitas
                            </Button>
                        </Link>
                    </CardContent>
                </Card>

                <!-- Despesas -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Despesas</CardTitle>
                        <TrendingDown class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(resumo.total_despesas) }}</div>
                        <p class="text-xs text-muted-foreground">
                            Pagas: {{ formatCurrency(resumo.despesas_pagas) }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Pendentes: {{ formatCurrency(resumo.despesas_pendentes) }}
                        </p>
                        <Link href="/despesas" class="mt-3 block">
                            <Button variant="outline" size="sm" class="w-full">
                                <CreditCard class="mr-2 h-4 w-4" />
                                Ver Despesas
                            </Button>
                        </Link>
                    </CardContent>
                </Card>

                <!-- Saldo -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Saldo</CardTitle>
                        <TrendingUp v-if="resumo.saldo >= 0" class="h-4 w-4 text-green-600" />
                        <TrendingDown v-else class="h-4 w-4 text-red-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold" :class="getSaldoColor">
                            {{ formatCurrency(resumo.saldo) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Receitas - Despesas
                        </p>
                        <div class="mt-2">
                            <Badge v-if="resumo.saldo > 0" variant="default">
                                Positivo
                            </Badge>
                            <Badge v-else-if="resumo.saldo < 0" variant="destructive">
                                Negativo
                            </Badge>
                            <Badge v-else variant="secondary">
                                Equilibrado
                            </Badge>
                        </div>
                    </CardContent>
                </Card>

                <!-- Despesas Vencidas -->
                <Card v-if="resumo.despesas_vencidas > 0">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Vencidas</CardTitle>
                        <TrendingDown class="h-4 w-4 text-red-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">
                            {{ formatCurrency(resumo.despesas_vencidas) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Despesas vencidas
                        </p>
                        <Badge variant="destructive" class="mt-2">
                            Atenção Necessária
                        </Badge>
                    </CardContent>
                </Card>
            </div>

            <!-- Listas de Receitas e Despesas -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Receitas Recentes -->
                <Card>
                    <CardHeader>
                        <CardTitle>Receitas Recentes</CardTitle>
                        <CardDescription>
                            Suas últimas receitas adicionadas
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="listas.receitas_recentes.length === 0" class="text-center py-6">
                            <p class="text-muted-foreground mb-4">
                                Nenhuma receita encontrada
                            </p>
                            <Link href="/receitas/create">
                                <Button variant="outline">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Adicionar Receita
                                </Button>
                            </Link>
                        </div>
                        
                        <div v-else class="space-y-3">
                            <div
                                v-for="receita in listas.receitas_recentes"
                                :key="receita.id"
                                class="flex items-center justify-between p-3 border rounded-lg"
                            >
                                <div class="flex-1">
                                    <p class="font-medium">{{ receita.descricao }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ formatDate(receita.created_at) }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Badge :variant="receita.status === 'recebido' ? 'default' : 'secondary'">
                                        {{ receita.status === 'recebido' ? 'Recebido' : 'Pendente' }}
                                    </Badge>
                                    <span class="font-semibold">{{ formatCurrency(parseFloat(receita.valor)) }}</span>
                                    <Link :href="`/receitas/${receita.id}`">
                                        <Button variant="outline" size="sm">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                            
                            <Separator />
                            
                            <Link href="/receitas" class="block">
                                <Button variant="outline" class="w-full">
                                    Ver Todas as Receitas
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <!-- Despesas Pendentes -->
                <Card>
                    <CardHeader>
                        <CardTitle>Despesas Pendentes</CardTitle>
                        <CardDescription>
                            Despesas que vencem em breve
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="listas.despesas_pendentes.length === 0" class="text-center py-6">
                            <p class="text-muted-foreground mb-4">
                                Nenhuma despesa pendente
                            </p>
                            <Link href="/despesas/create">
                                <Button>
                                    <Plus class="mr-2 h-4 w-4" />
                                    Adicionar Despesa
                                </Button>
                            </Link>
                        </div>
                        
                        <div v-else class="space-y-3">
                            <div
                                v-for="despesa in listas.despesas_pendentes"
                                :key="despesa.id"
                                class="flex items-center justify-between p-3 border rounded-lg"
                            >
                                <div class="flex-1">
                                    <p class="font-medium">{{ despesa.descricao }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        Vence em: {{ formatDate(despesa.data_vencimento) }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Badge variant="secondary">
                                        {{ despesa.categoria }}
                                    </Badge>
                                    <span class="font-semibold">{{ formatCurrency(parseFloat(despesa.valor)) }}</span>
                                    <Link :href="`/despesas/${despesa.id}`">
                                        <Button variant="outline" size="sm">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                            
                            <Separator />
                            
                            <Link href="/despesas" class="block">
                                <Button variant="outline" class="w-full">
                                    Ver Todas as Despesas
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
