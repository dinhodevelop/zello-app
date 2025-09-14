<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Plus, Eye, Edit, Trash2 } from 'lucide-vue-next';

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

defineProps<{
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

const getStatusBadgeVariant = (status: string) => {
    switch (status) {
        case 'pago':
            return 'default';
        case 'pendente':
            return 'secondary';
        case 'vencido':
            return 'destructive';
        default:
            return 'secondary';
    }
};

const getTipoBadgeVariant = (tipo: string) => {
    return tipo === 'fixa' ? 'outline' : 'secondary';
};

const formatCurrency = (value: string) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(parseFloat(value));
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-BR');
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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Despesas</h1>
                    <p class="text-muted-foreground">
                        Gerencie suas despesas e contas
                    </p>
                </div>
                <Link href="/despesas/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Despesa
                    </Button>
                </Link>
            </div>

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

            <div v-else class="grid gap-4">
                <Card v-for="despesa in despesas" :key="despesa.id">
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <CardTitle class="text-lg">{{ despesa.descricao }}</CardTitle>
                                <CardDescription>
                                    {{ despesa.categoria }}
                                </CardDescription>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Badge :variant="getTipoBadgeVariant(despesa.tipo)">
                                    {{ despesa.tipo === 'fixa' ? 'Fixa' : 'Variável' }}
                                </Badge>
                                <Badge :variant="getStatusBadgeVariant(despesa.status)">
                                    {{ despesa.status === 'pago' ? 'Pago' : despesa.status === 'pendente' ? 'Pendente' : 'Vencido' }}
                                </Badge>
                                <Badge v-if="despesa.recorrente" variant="outline">
                                    Recorrente
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Valor</p>
                                <p class="text-lg font-semibold">{{ formatCurrency(despesa.valor) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Vencimento</p>
                                <p class="text-sm">{{ formatDate(despesa.data_vencimento) }}</p>
                            </div>
                            <div v-if="despesa.data_pagamento">
                                <p class="text-sm font-medium text-muted-foreground">Pagamento</p>
                                <p class="text-sm">{{ formatDate(despesa.data_pagamento) }}</p>
                            </div>
                        </div>

                        <div v-if="despesa.observacoes" class="mt-4">
                            <p class="text-sm font-medium text-muted-foreground">Observações</p>
                            <p class="text-sm">{{ despesa.observacoes }}</p>
                        </div>

                        <Separator class="my-4" />

                        <div class="flex justify-end space-x-2">
                            <Link :href="`/despesas/${despesa.id}`">
                                <Button variant="outline" size="sm">
                                    <Eye class="mr-2 h-4 w-4" />
                                    Ver
                                </Button>
                            </Link>
                            <Link :href="`/despesas/${despesa.id}/edit`">
                                <Button variant="outline" size="sm">
                                    <Edit class="mr-2 h-4 w-4" />
                                    Editar
                                </Button>
                            </Link>
                            <Button
                                variant="outline"
                                size="sm"
                                @click="deleteDespesa(despesa.id)"
                                class="text-red-600 hover:text-red-700"
                            >
                                <Trash2 class="mr-2 h-4 w-4" />
                                Excluir
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
