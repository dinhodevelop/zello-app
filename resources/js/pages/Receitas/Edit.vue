<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';

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
    {
        title: 'Editar',
        href: `/receitas/${props.receita.id}/edit`,
    },
];

const form = useForm({
    descricao: props.receita.descricao,
    valor: props.receita.valor,
    tipo: props.receita.tipo,
    frequencia: props.receita.frequencia,
    status: props.receita.status,
    data_recebimento: props.receita.data_recebimento || '',
    data_vencimento: props.receita.data_vencimento || '',
    observacoes: props.receita.observacoes || '',
});

const tiposReceita = [
    { value: 'salario', label: 'Salário' },
    { value: 'freelance', label: 'Freelance' },
    { value: 'rendimento', label: 'Rendimento' },
    { value: 'outros', label: 'Outros' }
];

const frequencias = [
    { value: 'mensal', label: 'Mensal' },
    { value: 'semanal', label: 'Semanal' },
    { value: 'unica', label: 'Única' }
];

const submit = () => {
    form.put(`/receitas/${props.receita.id}`, {
        onSuccess: () => {
            // Redirect será feito pelo controller
        },
    });
};
</script>

<template>
    <Head title="Editar Receita" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Editar Receita</CardTitle>
                        <CardDescription>
                            Atualize as informações da receita
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Descrição -->
                            <div class="space-y-2">
                                <Label for="descricao">Descrição *</Label>
                                <Input
                                    id="descricao"
                                    v-model="form.descricao"
                                    type="text"
                                    placeholder="Ex: Salário da empresa XYZ"
                                    :class="{ 'border-red-500': form.errors.descricao }"
                                />
                                <p v-if="form.errors.descricao" class="text-sm text-red-500">
                                    {{ form.errors.descricao }}
                                </p>
                            </div>

                            <!-- Valor -->
                            <div class="space-y-2">
                                <Label for="valor">Valor (R$) *</Label>
                                <Input
                                    id="valor"
                                    v-model="form.valor"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0,00"
                                    :class="{ 'border-red-500': form.errors.valor }"
                                />
                                <p v-if="form.errors.valor" class="text-sm text-red-500">
                                    {{ form.errors.valor }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Tipo -->
                                <div class="space-y-2">
                                    <Label for="tipo">Tipo *</Label>
                                    <select
                                        id="tipo"
                                        v-model="form.tipo"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        :class="{ 'border-red-500': form.errors.tipo }"
                                    >
                                        <option v-for="tipo in tiposReceita" :key="tipo.value" :value="tipo.value">
                                            {{ tipo.label }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.tipo" class="text-sm text-red-500">
                                        {{ form.errors.tipo }}
                                    </p>
                                </div>

                                <!-- Frequência -->
                                <div class="space-y-2">
                                    <Label for="frequencia">Frequência *</Label>
                                    <select
                                        id="frequencia"
                                        v-model="form.frequencia"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        :class="{ 'border-red-500': form.errors.frequencia }"
                                    >
                                        <option v-for="freq in frequencias" :key="freq.value" :value="freq.value">
                                            {{ freq.label }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.frequencia" class="text-sm text-red-500">
                                        {{ form.errors.frequencia }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Status -->
                                <div class="space-y-2">
                                    <Label for="status">Status *</Label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        :class="{ 'border-red-500': form.errors.status }"
                                    >
                                        <option value="pendente">Pendente</option>
                                        <option value="recebido">Recebido</option>
                                    </select>
                                    <p v-if="form.errors.status" class="text-sm text-red-500">
                                        {{ form.errors.status }}
                                    </p>
                                </div>

                                <!-- Data de Vencimento -->
                                <div class="space-y-2">
                                    <Label for="data_vencimento">Data de Vencimento</Label>
                                    <Input
                                        id="data_vencimento"
                                        v-model="form.data_vencimento"
                                        type="date"
                                        :class="{ 'border-red-500': form.errors.data_vencimento }"
                                    />
                                    <p v-if="form.errors.data_vencimento" class="text-sm text-red-500">
                                        {{ form.errors.data_vencimento }}
                                    </p>
                                </div>
                            </div>

                            <!-- Data de Recebimento -->
                            <div class="space-y-2">
                                <Label for="data_recebimento">Data de Recebimento</Label>
                                <Input
                                    id="data_recebimento"
                                    v-model="form.data_recebimento"
                                    type="date"
                                    :class="{ 'border-red-500': form.errors.data_recebimento }"
                                />
                                <p v-if="form.errors.data_recebimento" class="text-sm text-red-500">
                                    {{ form.errors.data_recebimento }}
                                </p>
                            </div>

                            <!-- Observações -->
                            <div class="space-y-2">
                                <Label for="observacoes">Observações</Label>
                                <textarea
                                    id="observacoes"
                                    v-model="form.observacoes"
                                    rows="3"
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="Observações adicionais..."
                                    :class="{ 'border-red-500': form.errors.observacoes }"
                                />
                                <p v-if="form.errors.observacoes" class="text-sm text-red-500">
                                    {{ form.errors.observacoes }}
                                </p>
                            </div>

                            <Separator />

                            <div class="flex justify-end space-x-4">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="$inertia.visit(`/receitas/${receita.id}`)"
                                >
                                    Cancelar
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Salvando...' : 'Salvar Alterações' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
