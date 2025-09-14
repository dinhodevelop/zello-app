<template>
    <AppLayout title="Criar Usuário">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Criar Novo Usuário
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:p-8">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                Informações do Usuário
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Crie um novo usuário e escolha se deseja adicioná-lo ao seu lar atual.
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Nome -->
                            <div>
                                <Label for="name">Nome Completo *</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Ex: João Silva"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <Label for="email">E-mail *</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    placeholder="Ex: joao@exemplo.com"
                                    required
                                />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <!-- Senha -->
                            <div>
                                <Label for="password">Senha *</Label>
                                <Input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    placeholder="Mínimo 8 caracteres"
                                    required
                                />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>

                            <!-- Confirmar Senha -->
                            <div>
                                <Label for="password_confirmation">Confirmar Senha *</Label>
                                <Input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    placeholder="Repita a senha"
                                    required
                                />
                                <InputError :message="form.errors.password_confirmation" class="mt-2" />
                            </div>

                            <!-- Adicionar ao Lar -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex items-start space-x-3">
                                    <Checkbox
                                        id="add_to_household"
                                        v-model:checked="form.add_to_household"
                                        class="mt-1"
                                    />
                                    <div class="flex-1">
                                        <Label for="add_to_household" class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                            Adicionar ao meu lar atual
                                        </Label>
                                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                            Se marcado, o usuário será automaticamente adicionado ao seu lar atual.
                                            Caso contrário, ficará sem lar e poderá escolher um posteriormente.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex items-center justify-between space-x-4">
                                <Button variant="outline" as-child>
                                    <Link :href="userManagementIndex().url">
                                        Cancelar
                                    </Link>
                                </Button>
                                
                                <Button 
                                    type="submit"
                                    :class="{ 'opacity-25': form.processing }" 
                                    :disabled="form.processing"
                                >
                                    <UserPlus class="w-4 h-4 mr-2" />
                                    <span v-if="form.processing">Criando...</span>
                                    <span v-else>Criar Usuário</span>
                                </Button>
                            </div>
                        </form>

                        <!-- Informações Adicionais -->
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                            Informações Importantes
                                        </h3>
                                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li>O usuário receberá as credenciais por e-mail (funcionalidade futura)</li>
                                                <li>Você pode gerenciar os lares dos usuários a qualquer momento</li>
                                                <li>Usuários podem ser movidos entre lares conforme necessário</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Checkbox } from '@/components/ui/checkbox'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { UserPlus } from 'lucide-vue-next'
import { store as userManagementStore, index as userManagementIndex } from '@/routes/user-management'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    add_to_household: true
})

const submit = () => {
    form.post(userManagementStore().url, {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        },
    })
}
</script>
