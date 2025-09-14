<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Edit, ArrowLeft, Trash2, User as UserIcon, UserCheck } from 'lucide-vue-next';

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
    creator?: User;
    responsible_user?: User;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    despesa: Despesa;
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
    {
        title: props.despesa.descricao,
        href: `/despesas/${props.despesa.id}`,
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

const formatDateTime = (date: string) => {
    return new Date(date).toLocaleString('pt-BR');
};
</script>

<template>
    <Head :title="despesa.descricao" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <Link href="/despesas">
                            <Button variant="outline" size="sm">
                                <ArrowLeft class="mr-2 h-4 w-4" />
                                Voltar
                            </Button>
                        </Link>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight">{{ despesa.descricao }}</h1>
                            <p class="text-muted-foreground">
                                Detalhes da despesa
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Link :href="`/despesas/${despesa.id}/edit`">
                            <Button variant="outline">
                                <Edit class="mr-2 h-4 w-4" />
                                Editar
                            </Button>
                        </Link>
                    </div>
                </div>

                <!-- Informações Principais -->
                <Card class="mb-6">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="text-xl">{{ despesa.descricao }}</CardTitle>
                                <CardDescription>{{ despesa.categoria }}</CardDescription>
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
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground mb-2">Valor</h3>
                                <p class="text-2xl font-bold">{{ formatCurrency(despesa.valor) }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground mb-2">Data de Vencimento</h3>
                                <p class="text-lg">{{ formatDate(despesa.data_vencimento) }}</p>
                            </div>
                            <div v-if="despesa.data_pagamento">
                                <h3 class="text-sm font-medium text-muted-foreground mb-2">Data de Pagamento</h3>
                                <p class="text-lg">{{ formatDate(despesa.data_pagamento) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Observações -->
                <Card v-if="despesa.observacoes" class="mb-6">
                    <CardHeader>
                        <CardTitle>Observações</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm leading-relaxed">{{ despesa.observacoes }}</p>
                    </CardContent>
                </Card>

                <!-- Informações de Autoria -->
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle>Informações de Autoria</CardTitle>
                        <CardDescription>
                            Quem criou e quem é responsável por esta despesa
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div v-if="despesa.creator" class="flex items-center space-x-3 p-3 bg-muted/30 rounded-lg">
                                <UserIcon class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground">Criado por</h3>
                                    <p class="text-base font-medium">{{ despesa.creator.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ despesa.creator.email }}</p>
                                </div>
                            </div>
                            <div v-if="despesa.responsible_user" class="flex items-center space-x-3 p-3 bg-muted/30 rounded-lg">
                                <UserCheck class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground">Responsável</h3>
                                    <p class="text-base font-medium">{{ despesa.responsible_user.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ despesa.responsible_user.email }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Informações do Sistema -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informações do Sistema</CardTitle>
                        <CardDescription>
                            Dados de criação e atualização
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground mb-1">Criado em</h3>
                                <p class="text-sm">{{ formatDateTime(despesa.created_at) }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground mb-1">Última atualização</h3>
                                <p class="text-sm">{{ formatDateTime(despesa.updated_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
