<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Checkbox } from '@/components/ui/checkbox';
import { ref } from 'vue';

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
        title: 'Nova Despesa',
        href: '/despesas/create',
    },
];

const form = useForm({
    descricao: '',
    valor: '',
    tipo: 'fixa',
    categoria: '',
    status: 'pendente',
    data_pagamento: '',
    data_vencimento: '',
    recorrente: false,
    observacoes: '',
});

const categorias = [
    'Aluguel',
    'Financiamento',
    'Assinatura',
    'Mercado',
    'Combustível',
    'Lazer',
    'Saúde',
    'Educação',
    'Transporte',
    'Outros'
];

const submit = () => {
    form.post('/despesas', {
        onSuccess: () => {
            // Redirect será feito pelo controller
        },
    });
};
</script>

<template>
    <Head title="Nova Despesa" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Nova Despesa</CardTitle>
                        <CardDescription>
                            Adicione uma nova despesa ao seu controle financeiro
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
                                    placeholder="Ex: Aluguel do apartamento"
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
                                        <option value="fixa">Fixa</option>
                                        <option value="variavel">Variável</option>
                                    </select>
                                    <p v-if="form.errors.tipo" class="text-sm text-red-500">
                                        {{ form.errors.tipo }}
                                    </p>
                                </div>

                                <!-- Categoria -->
                                <div class="space-y-2">
                                    <Label for="categoria">Categoria *</Label>
                                    <select
                                        id="categoria"
                                        v-model="form.categoria"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        :class="{ 'border-red-500': form.errors.categoria }"
                                    >
                                        <option value="">Selecione uma categoria</option>
                                        <option v-for="categoria in categorias" :key="categoria" :value="categoria">
                                            {{ categoria }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.categoria" class="text-sm text-red-500">
                                        {{ form.errors.categoria }}
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
                                        <option value="pago">Pago</option>
                                        <option value="vencido">Vencido</option>
                                    </select>
                                    <p v-if="form.errors.status" class="text-sm text-red-500">
                                        {{ form.errors.status }}
                                    </p>
                                </div>

                                <!-- Data de Vencimento -->
                                <div class="space-y-2">
                                    <Label for="data_vencimento">Data de Vencimento *</Label>
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

                            <!-- Data de Pagamento -->
                            <div class="space-y-2">
                                <Label for="data_pagamento">Data de Pagamento</Label>
                                <Input
                                    id="data_pagamento"
                                    v-model="form.data_pagamento"
                                    type="date"
                                    :class="{ 'border-red-500': form.errors.data_pagamento }"
                                />
                                <p v-if="form.errors.data_pagamento" class="text-sm text-red-500">
                                    {{ form.errors.data_pagamento }}
                                </p>
                            </div>

                            <!-- Recorrente -->
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="recorrente"
                                    :checked="form.recorrente"
                                    @update:checked="form.recorrente = $event"
                                />
                                <Label for="recorrente">Despesa recorrente</Label>
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
                                    @click="$inertia.visit('/despesas')"
                                >
                                    Cancelar
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Salvando...' : 'Salvar Despesa' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
