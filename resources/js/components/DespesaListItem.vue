<script setup lang="ts">
import { Badge } from './ui/badge';
import { Button } from './ui/button';
import { Link } from '@inertiajs/vue3';
import { Eye, Edit, Trash2, Calendar, DollarSign, Repeat } from 'lucide-vue-next';

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

const formatCurrency = (value: string) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(parseFloat(value));
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const getStatusLabel = (status: string) => {
    const labels = {
        'pago': 'Pago',
        'pendente': 'Pendente',
        'vencido': 'Vencido'
    };
    return labels[status as keyof typeof labels] || status;
};

const isOverdue = (status: string, dataVencimento: string) => {
    if (status === 'pago') return false;
    const today = new Date();
    const vencimento = new Date(dataVencimento);
    return vencimento < today;
};

const handleDelete = () => {
    emit('delete', props.despesa.id);
};
</script>

<template>
    <div 
        class="flex items-center justify-between p-4 border rounded-lg hover:bg-accent/50 transition-colors"
        :class="{ 'border-error bg-error/5': isOverdue(despesa.status, despesa.data_vencimento) }"
    >
        <!-- Informações principais -->
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                        <h3 class="font-medium text-foreground truncate">{{ despesa.descricao }}</h3>
                        <Repeat v-if="despesa.recorrente" class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <p class="text-sm text-muted-foreground">{{ despesa.categoria }}</p>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                    <Badge :class="getStatusBadgeClass(despesa.status)" class="text-xs">
                        {{ getStatusLabel(despesa.status) }}
                    </Badge>
                    <Badge variant="outline" class="text-xs">
                        {{ despesa.tipo === 'fixa' ? 'Fixa' : 'Variável' }}
                    </Badge>
                </div>
            </div>
            
            <!-- Detalhes secundários -->
            <div class="flex items-center space-x-4 mt-2 text-sm text-muted-foreground">
                <div class="flex items-center space-x-1">
                    <DollarSign class="h-3 w-3" />
                    <span class="font-semibold text-expense">{{ formatCurrency(despesa.valor) }}</span>
                </div>
                <div class="flex items-center space-x-1">
                    <Calendar class="h-3 w-3" />
                    <span 
                        :class="{ 'text-error font-medium': isOverdue(despesa.status, despesa.data_vencimento) }"
                    >
                        Vence: {{ formatDate(despesa.data_vencimento) }}
                    </span>
                </div>
                <div v-if="despesa.data_pagamento" class="flex items-center space-x-1">
                    <Calendar class="h-3 w-3" />
                    <span>Pago: {{ formatDate(despesa.data_pagamento) }}</span>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="flex items-center space-x-1 ml-4">
            <Link :href="`/despesas/${despesa.id}`">
                <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                    <Eye class="h-4 w-4" />
                    <span class="sr-only">Ver despesa</span>
                </Button>
            </Link>
            <Link :href="`/despesas/${despesa.id}/edit`">
                <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                    <Edit class="h-4 w-4" />
                    <span class="sr-only">Editar despesa</span>
                </Button>
            </Link>
            <Button
                variant="ghost"
                size="sm"
                @click="handleDelete"
                class="h-8 w-8 p-0 text-error hover:bg-error hover:text-error-foreground"
            >
                <Trash2 class="h-4 w-4" />
                <span class="sr-only">Excluir despesa</span>
            </Button>
        </div>
    </div>
</template>
