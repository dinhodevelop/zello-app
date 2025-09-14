<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Link } from '@inertiajs/vue3';
import { Eye, Edit, Trash2, Calendar, DollarSign, Clock, CheckCircle } from 'lucide-vue-next';

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
    <Card class="hover:shadow-md transition-shadow">
        <CardHeader class="pb-3">
            <div class="flex items-start justify-between">
                <div class="space-y-1 flex-1 min-w-0">
                    <CardTitle class="text-lg truncate">{{ receita.descricao }}</CardTitle>
                    <CardDescription class="flex items-center space-x-2">
                        <span>{{ getTipoLabel(receita.tipo) }}</span>
                        <span class="text-xs">•</span>
                        <span>{{ getFrequenciaLabel(receita.frequencia) }}</span>
                    </CardDescription>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                    <Badge :class="getStatusBadgeClass(receita.status)">
                        <CheckCircle v-if="receita.status === 'recebido'" class="mr-1 h-3 w-3" />
                        <Clock v-else class="mr-1 h-3 w-3" />
                        {{ receita.status === 'recebido' ? 'Recebido' : 'Pendente' }}
                    </Badge>
                </div>
            </div>
        </CardHeader>
        
        <CardContent>
            <!-- Valor destacado -->
            <div class="mb-4">
                <div class="flex items-center space-x-2">
                    <DollarSign class="h-5 w-5 text-income" />
                    <span class="text-2xl font-bold text-income">{{ formatCurrency(receita.valor) }}</span>
                </div>
            </div>

            <!-- Informações de data -->
            <div class="grid grid-cols-1 gap-3 mb-4">
                <div v-if="receita.data_vencimento" class="flex items-center justify-between p-2 bg-muted/30 rounded-md">
                    <div class="flex items-center space-x-2">
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                        <span class="text-sm font-medium">Vencimento</span>
                    </div>
                    <span class="text-sm">{{ formatDate(receita.data_vencimento) }}</span>
                </div>
                
                <div v-if="receita.data_recebimento" class="flex items-center justify-between p-2 bg-confirmed/10 rounded-md">
                    <div class="flex items-center space-x-2">
                        <CheckCircle class="h-4 w-4 text-confirmed" />
                        <span class="text-sm font-medium">Recebimento</span>
                    </div>
                    <span class="text-sm">{{ formatDate(receita.data_recebimento) }}</span>
                </div>
            </div>

            <!-- Observações -->
            <div v-if="receita.observacoes" class="mb-4">
                <p class="text-sm font-medium text-muted-foreground mb-1">Observações</p>
                <p class="text-sm bg-muted/30 p-2 rounded-md">{{ receita.observacoes }}</p>
            </div>

            <Separator class="my-4" />

            <!-- Ações -->
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
