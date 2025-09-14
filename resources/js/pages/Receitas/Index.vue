<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Plus, Eye, Edit, Trash2 } from 'lucide-vue-next';

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

defineProps<{
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

const getStatusBadgeVariant = (status: string) => {
    switch (status) {
        case 'recebido':
            return 'default';
        case 'pendente':
            return 'secondary';
        default:
            return 'secondary';
    }
};

const getTipoBadgeVariant = (tipo: string) => {
    switch (tipo) {
        case 'salario':
            return 'default';
        case 'freelance':
            return 'secondary';
        case 'rendimento':
            return 'outline';
        default:
            return 'outline';
    }
};

const getFrequenciaBadgeVariant = (frequencia: string) => {
    return frequencia === 'mensal' ? 'outline' : 'secondary';
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

const getTipoLabel = (tipo: string) => {
    const labels = {
        'salario': 'Salário',
        'freelance': 'Freelance',
        'rendimento': 'Rendimento',
        'outros': 'Outros'
    };
    return labels[tipo as keyof typeof labels] || tipo;
};

const getFrequenciaLabel = (frequencia: string) => {
    const labels = {
        'mensal': 'Mensal',
        'semanal': 'Semanal',
        'unica': 'Única'
    };
    return labels[frequencia as keyof typeof labels] || frequencia;
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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Receitas</h1>
                    <p class="text-muted-foreground">
                        Gerencie suas receitas e rendimentos
                    </p>
                </div>
                <Link href="/receitas/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Receita
                    </Button>
                </Link>
            </div>

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

            <div v-else class="grid gap-4">
                <Card v-for="receita in receitas" :key="receita.id">
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <CardTitle class="text-lg">{{ receita.descricao }}</CardTitle>
                                <CardDescription>
                                    {{ getTipoLabel(receita.tipo) }}
                                </CardDescription>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Badge :variant="getTipoBadgeVariant(receita.tipo)">
                                    {{ getTipoLabel(receita.tipo) }}
                                </Badge>
                                <Badge :variant="getFrequenciaBadgeVariant(receita.frequencia)">
                                    {{ getFrequenciaLabel(receita.frequencia) }}
                                </Badge>
                                <Badge :variant="getStatusBadgeVariant(receita.status)">
                                    {{ receita.status === 'recebido' ? 'Recebido' : 'Pendente' }}
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Valor</p>
                                <p class="text-lg font-semibold">{{ formatCurrency(receita.valor) }}</p>
                            </div>
                            <div v-if="receita.data_vencimento">
                                <p class="text-sm font-medium text-muted-foreground">Vencimento</p>
                                <p class="text-sm">{{ formatDate(receita.data_vencimento) }}</p>
                            </div>
                            <div v-if="receita.data_recebimento">
                                <p class="text-sm font-medium text-muted-foreground">Recebimento</p>
                                <p class="text-sm">{{ formatDate(receita.data_recebimento) }}</p>
                            </div>
                        </div>

                        <div v-if="receita.observacoes" class="mt-4">
                            <p class="text-sm font-medium text-muted-foreground">Observações</p>
                            <p class="text-sm">{{ receita.observacoes }}</p>
                        </div>

                        <Separator class="my-4" />

                        <div class="flex justify-end space-x-2">
                            <Link :href="`/receitas/${receita.id}`">
                                <Button variant="outline" size="sm">
                                    <Eye class="mr-2 h-4 w-4" />
                                    Ver
                                </Button>
                            </Link>
                            <Link :href="`/receitas/${receita.id}/edit`">
                                <Button variant="outline" size="sm">
                                    <Edit class="mr-2 h-4 w-4" />
                                    Editar
                                </Button>
                            </Link>
                            <Button
                                variant="outline"
                                size="sm"
                                @click="deleteReceita(receita.id)"
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
