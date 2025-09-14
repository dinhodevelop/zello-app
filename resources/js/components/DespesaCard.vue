<script setup lang="ts">
import { Badge } from './ui/badge';
import { Button } from './ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from './ui/card';
import { Separator } from './ui/separator';
import { Link } from '@inertiajs/vue3';
import { Eye, Edit, Trash2, Calendar, DollarSign, Clock, CheckCircle, AlertTriangle, Repeat, User as UserIcon, UserCheck } from 'lucide-vue-next';
import type { User } from '../types';

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

interface Props {
    despesa: Despesa;
}

interface Emits {
    (e: 'delete', id: number): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const getStatusBadgeClass = (status: string) => {
    switch (status) {
        case 'pago':
            return 'bg-confirmed text-confirmed-foreground';
        case 'pendente':
            return 'bg-pending text-pending-foreground';
        case 'vencido':
            return 'bg-error text-error-foreground';
        default:
            return 'bg-pending text-pending-foreground';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'pago':
            return CheckCircle;
        case 'vencido':
            return AlertTriangle;
        default:
            return Clock;
    }
};

const getStatusLabel = (status: string) => {
    const labels = {
        'pago': 'Pago',
        'pendente': 'Pendente',
        'vencido': 'Vencido'
    };
    return labels[status as keyof typeof labels] || status;
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

const isOverdue = (status: string, dataVencimento: string) => {
    if (status === 'pago') return false;
    const today = new Date();
    const vencimento = new Date(dataVencimento);
    return vencimento < today;
};

const getDaysUntilDue = (dataVencimento: string) => {
    const today = new Date();
    const vencimento = new Date(dataVencimento);
    const diffTime = vencimento.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
};

const handleDelete = () => {
    emit('delete', props.despesa.id);
};
</script>

<template>
    <Card 
        class="hover:shadow-md transition-shadow"
        :class="{ 'border-error bg-error/5': isOverdue(despesa.status, despesa.data_vencimento) }"
    >
        <CardHeader class="pb-3">
            <div class="flex items-start justify-between">
                <div class="space-y-1 flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                        <CardTitle class="text-lg truncate">{{ despesa.descricao }}</CardTitle>
                        <Repeat v-if="despesa.recorrente" class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <CardDescription class="flex items-center space-x-2">
                        <span>{{ despesa.categoria }}</span>
                        <span class="text-xs">•</span>
                        <span>{{ despesa.tipo === 'fixa' ? 'Fixa' : 'Variável' }}</span>
                    </CardDescription>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                    <Badge :class="getStatusBadgeClass(despesa.status)">
                        <component :is="getStatusIcon(despesa.status)" class="mr-1 h-3 w-3" />
                        {{ getStatusLabel(despesa.status) }}
                    </Badge>
                </div>
            </div>
        </CardHeader>
        
        <CardContent>
            <!-- Valor destacado -->
            <div class="mb-4">
                <div class="flex items-center space-x-2">
                    <DollarSign class="h-5 w-5 text-expense" />
                    <span class="text-2xl font-bold text-expense">{{ formatCurrency(despesa.valor) }}</span>
                </div>
            </div>

            <!-- Informações de data -->
            <div class="grid grid-cols-1 gap-3 mb-4">
                <div 
                    class="flex items-center justify-between p-2 rounded-md"
                    :class="isOverdue(despesa.status, despesa.data_vencimento) ? 'bg-error/10' : 'bg-muted/30'"
                >
                    <div class="flex items-center space-x-2">
                        <Calendar 
                            class="h-4 w-4"
                            :class="isOverdue(despesa.status, despesa.data_vencimento) ? 'text-error' : 'text-muted-foreground'"
                        />
                        <span class="text-sm font-medium">Vencimento</span>
                        <span 
                            v-if="despesa.status !== 'pago' && getDaysUntilDue(despesa.data_vencimento) <= 7 && getDaysUntilDue(despesa.data_vencimento) >= 0"
                            class="text-xs bg-warning text-warning-foreground px-2 py-0.5 rounded-full"
                        >
                            {{ getDaysUntilDue(despesa.data_vencimento) }} dias
                        </span>
                    </div>
                    <span 
                        class="text-sm"
                        :class="isOverdue(despesa.status, despesa.data_vencimento) ? 'text-error font-medium' : ''"
                    >
                        {{ formatDate(despesa.data_vencimento) }}
                    </span>
                </div>
                
                <div v-if="despesa.data_pagamento" class="flex items-center justify-between p-2 bg-confirmed/10 rounded-md">
                    <div class="flex items-center space-x-2">
                        <CheckCircle class="h-4 w-4 text-confirmed" />
                        <span class="text-sm font-medium">Pagamento</span>
                    </div>
                    <span class="text-sm">{{ formatDate(despesa.data_pagamento) }}</span>
                </div>
            </div>

            <!-- Informações de Autoria -->
            <div class="grid grid-cols-1 gap-2 mb-4">
                <div v-if="despesa.creator" class="flex items-center justify-between p-2 bg-muted/20 rounded-md">
                    <div class="flex items-center space-x-2">
                        <UserIcon class="h-4 w-4 text-muted-foreground" />
                        <span class="text-sm font-medium">Criado por</span>
                    </div>
                    <span class="text-sm">{{ despesa.creator.name }}</span>
                </div>
                
                <div v-if="despesa.responsible_user" class="flex items-center justify-between p-2 bg-muted/20 rounded-md">
                    <div class="flex items-center space-x-2">
                        <UserCheck class="h-4 w-4 text-muted-foreground" />
                        <span class="text-sm font-medium">Responsável</span>
                    </div>
                    <span class="text-sm">{{ despesa.responsible_user.name }}</span>
                </div>
            </div>

            <!-- Observações -->
            <div v-if="despesa.observacoes" class="mb-4">
                <p class="text-sm font-medium text-muted-foreground mb-1">Observações</p>
                <p class="text-sm bg-muted/30 p-2 rounded-md">{{ despesa.observacoes }}</p>
            </div>

            <Separator class="my-4" />

            <!-- Ações -->
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
                    @click="handleDelete"
                    class="text-error hover:bg-error hover:text-error-foreground"
                >
                    <Trash2 class="mr-2 h-4 w-4" />
                    Excluir
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
