<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
// import { Separator } from '@/components/ui/separator'; // Removido - não utilizado
import { Edit, ArrowLeft, User as UserIcon, UserCheck } from 'lucide-vue-next';

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
    creator?: User;
    responsible_user?: User;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    receita: Receita;
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
    {
        title: props.receita.descricao,
        href: `/receitas/${props.receita.id}`,
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

const formatDateTime = (date: string) => {
    return new Date(date).toLocaleString('pt-BR');
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
</script>

<template>
    <Head :title="receita.descricao" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <Link href="/receitas">
                            <Button variant="outline" size="sm">
                                <ArrowLeft class="mr-2 h-4 w-4" />
                                Voltar
                            </Button>
                        </Link>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight">{{ receita.descricao }}</h1>
                            <p class="text-muted-foreground">
                                Detalhes da receita
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Link :href="`/receitas/${receita.id}/edit`">
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
                                <CardTitle class="text-xl">{{ receita.descricao }}</CardTitle>
                                <CardDescription>{{ getTipoLabel(receita.tipo) }}</CardDescription>
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
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground mb-2">Valor</h3>
                                <p class="text-2xl font-bold">{{ formatCurrency(receita.valor) }}</p>
                            </div>
                            <div v-if="receita.data_vencimento">
                                <h3 class="text-sm font-medium text-muted-foreground mb-2">Data de Vencimento</h3>
                                <p class="text-lg">{{ formatDate(receita.data_vencimento) }}</p>
                            </div>
                            <div v-if="receita.data_recebimento">
                                <h3 class="text-sm font-medium text-muted-foreground mb-2">Data de Recebimento</h3>
                                <p class="text-lg">{{ formatDate(receita.data_recebimento) }}</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground mb-2">Frequência</h3>
                                <p class="text-sm">{{ getFrequenciaLabel(receita.frequencia) }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground mb-2">Status</h3>
                                <p class="text-sm">{{ receita.status === 'recebido' ? 'Recebido' : 'Pendente' }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Observações -->
                <Card v-if="receita.observacoes" class="mb-6">
                    <CardHeader>
                        <CardTitle>Observações</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm leading-relaxed">{{ receita.observacoes }}</p>
                    </CardContent>
                </Card>

                <!-- Informações de Autoria -->
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle>Informações de Autoria</CardTitle>
                        <CardDescription>
                            Quem criou e quem é responsável por esta receita
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div v-if="receita.creator" class="flex items-center space-x-3 p-3 bg-muted/30 rounded-lg">
                                <UserIcon class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground">Criado por</h3>
                                    <p class="text-base font-medium">{{ receita.creator.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ receita.creator.email }}</p>
                                </div>
                            </div>
                            <div v-if="receita.responsible_user" class="flex items-center space-x-3 p-3 bg-muted/30 rounded-lg">
                                <UserCheck class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground">Responsável</h3>
                                    <p class="text-base font-medium">{{ receita.responsible_user.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ receita.responsible_user.email }}</p>
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
                                <p class="text-sm">{{ formatDateTime(receita.created_at) }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground mb-1">Última atualização</h3>
                                <p class="text-sm">{{ formatDateTime(receita.updated_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
