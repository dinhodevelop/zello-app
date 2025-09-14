<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { Eye, Edit, Trash2, Calendar, DollarSign } from 'lucide-vue-next';

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

interface Props {
    receita: Receita;
}

interface Emits {
    (e: 'delete', id: number): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const getStatusBadgeClass = (status: string) => {
    switch (status) {
        case 'recebido':
            return 'bg-confirmed text-confirmed-foreground';
        case 'pendente':
            return 'bg-pending text-pending-foreground';
        default:
            return 'bg-pending text-pending-foreground';
    }
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

const formatCurrency = (value: string) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(parseFloat(value));
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const handleDelete = () => {
    emit('delete', props.receita.id);
};
</script>

<template>
    <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-accent/50 transition-colors">
        <!-- Informações principais -->
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-foreground truncate">{{ receita.descricao }}</h3>
                    <p class="text-sm text-muted-foreground">{{ getTipoLabel(receita.tipo) }}</p>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                    <Badge :class="getStatusBadgeClass(receita.status)" class="text-xs">
                        {{ receita.status === 'recebido' ? 'Recebido' : 'Pendente' }}
                    </Badge>
                    <Badge variant="outline" class="text-xs">
                        {{ getFrequenciaLabel(receita.frequencia) }}
                    </Badge>
                </div>
            </div>
            
            <!-- Detalhes secundários -->
            <div class="flex items-center space-x-4 mt-2 text-sm text-muted-foreground">
                <div class="flex items-center space-x-1">
                    <DollarSign class="h-3 w-3" />
                    <span class="font-semibold text-income">{{ formatCurrency(receita.valor) }}</span>
                </div>
                <div v-if="receita.data_vencimento" class="flex items-center space-x-1">
                    <Calendar class="h-3 w-3" />
                    <span>Vence: {{ formatDate(receita.data_vencimento) }}</span>
                </div>
                <div v-if="receita.data_recebimento" class="flex items-center space-x-1">
                    <Calendar class="h-3 w-3" />
                    <span>Recebido: {{ formatDate(receita.data_recebimento) }}</span>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="flex items-center space-x-1 ml-4">
            <Link :href="`/receitas/${receita.id}`">
                <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                    <Eye class="h-4 w-4" />
                    <span class="sr-only">Ver receita</span>
                </Button>
            </Link>
            <Link :href="`/receitas/${receita.id}/edit`">
                <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                    <Edit class="h-4 w-4" />
                    <span class="sr-only">Editar receita</span>
                </Button>
            </Link>
            <Button
                variant="ghost"
                size="sm"
                @click="handleDelete"
                class="h-8 w-8 p-0 text-error hover:bg-error hover:text-error-foreground"
            >
                <Trash2 class="h-4 w-4" />
                <span class="sr-only">Excluir receita</span>
            </Button>
        </div>
    </div>
</template>
