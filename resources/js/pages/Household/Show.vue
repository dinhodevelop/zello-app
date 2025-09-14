<template>
    <AppLayout title="Configurações do Lar">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Configurações do Lar
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Informações do Lar -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ household.name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Criado em {{ formatDate(household.created_at) }}
                                </p>
                            </div>
                            <div class="flex space-x-3">
                                <Button variant="outline" @click="showEditModal = true">
                                    Editar Nome
                                </Button>
                                <Button variant="destructive" @click="showLeaveModal = true">
                                    Sair do Lar
                                </Button>
                            </div>
                        </div>

                        <!-- Membros do Lar -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                Membros ({{ household.users.length }})
                            </h4>
                            <div class="space-y-3">
                                <div 
                                    v-for="user in household.users" 
                                    :key="user.id"
                                    class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ user.name }}
                                                <span v-if="user.id === household.created_by" class="text-xs text-blue-600 dark:text-blue-400 ml-2">
                                                    (Criador)
                                                </span>
                                                <span v-if="user.id === $page.props.auth.user.id" class="text-xs text-green-600 dark:text-green-400 ml-2">
                                                    (Você)
                                                </span>
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ user.email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informações sobre convites -->
                        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                        Sistema de Convites
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                        <p>O sistema de convites será implementado em uma versão futura. Por enquanto, novos membros podem ser adicionados diretamente pelo administrador do sistema.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Edição -->
        <Dialog v-model:open="showEditModal">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Editar Nome do Lar</DialogTitle>
                </DialogHeader>
                
                <div class="mt-4">
                    <Label for="household_name">Nome do Lar</Label>
                    <Input
                        id="household_name"
                        v-model="editForm.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError :message="editForm.errors.name" class="mt-2" />
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="showEditModal = false">
                        Cancelar
                    </Button>
                    <Button
                        :class="{ 'opacity-25': editForm.processing }"
                        :disabled="editForm.processing"
                        @click="updateHousehold"
                    >
                        Salvar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Modal de Confirmação para Sair -->
        <Dialog v-model:open="showLeaveModal">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Sair do Lar Familiar</DialogTitle>
                </DialogHeader>
                
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Tem certeza de que deseja sair do lar "{{ household.name }}"? Você perderá acesso a todas as transações deste lar e precisará criar um novo lar ou ser convidado para outro.
                </p>

                <DialogFooter>
                    <Button variant="outline" @click="showLeaveModal = false">
                        Cancelar
                    </Button>
                    <Button
                        variant="destructive"
                        :class="{ 'opacity-25': leaveForm.processing }"
                        :disabled="leaveForm.processing"
                        @click="leaveHousehold"
                    >
                        Sair do Lar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { update, leave } from '@/routes/household'

const props = defineProps({
    household: Object
})

const showEditModal = ref(false)
const showLeaveModal = ref(false)

const editForm = useForm({
    name: props.household.name
})

const leaveForm = useForm({})

const updateHousehold = () => {
    editForm.put(update(props.household.id).url, {
        onSuccess: () => {
            showEditModal.value = false
        }
    })
}

const leaveHousehold = () => {
    leaveForm.post(leave().url, {
        onSuccess: () => {
            showLeaveModal.value = false
        }
    })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}
</script>
