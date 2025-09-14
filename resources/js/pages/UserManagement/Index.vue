<template>
    <AppLayout title="Gerenciar Usuários">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gerenciar Usuários
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Header com ações -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Lar: {{ household.name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Gerencie os membros do seu lar e crie novos usuários
                                </p>
                            </div>
                            <Button as-child>
                                <Link :href="userManagementCreate().url">
                                    <UserPlus class="w-4 h-4 mr-2" />
                                    Criar Usuário
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Membros do Lar -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Membros do Lar ({{ members.length }})
                        </h4>
                        
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div 
                                v-for="member in members" 
                                :key="member.id"
                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4"
                            >
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 dark:text-blue-400 font-medium">
                                                {{ member.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-white">
                                                {{ member.name }}
                                                <span v-if="member.id === household.created_by" class="text-xs text-blue-600 dark:text-blue-400 ml-1">
                                                    (Criador)
                                                </span>
                                            </h5>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ member.email }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <Badge :variant="member.role === 'admin' ? 'default' : 'secondary'" class="text-xs">
                                                    {{ member.role === 'admin' ? 'Admin' : 'Usuário' }}
                                                </Badge>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <div class="flex gap-2">
                                        <Button 
                                            variant="outline" 
                                            size="sm"
                                            @click="changeRole(member)"
                                            :disabled="member.id === $page.props.auth.user.id"
                                            class="flex-1"
                                        >
                                            <Shield class="w-4 h-4 mr-1" />
                                            {{ member.role === 'admin' ? 'Remover Admin' : 'Tornar Admin' }}
                                        </Button>
                                    </div>
                                    <Button 
                                        variant="outline" 
                                        size="sm"
                                        @click="removeMember(member)"
                                        :disabled="member.id === household.created_by || member.id === $page.props.auth.user.id"
                                        class="w-full"
                                    >
                                        <UserMinus class="w-4 h-4 mr-1" />
                                        Remover
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usuários Disponíveis -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Usuários Disponíveis ({{ availableUsers.length }})
                        </h4>
                        
                        <div v-if="availableUsers.length === 0" class="text-center py-8">
                            <Users class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                            <p class="text-gray-500 dark:text-gray-400">
                                Não há usuários disponíveis para adicionar.
                            </p>
                            <Button as-child class="mt-4">
                                <Link :href="userManagementCreate().url">
                                    Criar Novo Usuário
                                </Link>
                            </Button>
                        </div>
                        
                        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div 
                                v-for="user in availableUsers" 
                                :key="user.id"
                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4"
                            >
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                            <span class="text-gray-600 dark:text-gray-400 font-medium">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-white">
                                                {{ user.name }}
                                            </h5>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ user.email }}
                                            </p>
                                            <p v-if="user.household" class="text-xs text-orange-600 dark:text-orange-400">
                                                Lar: {{ user.household?.name }}
                                            </p>
                                            <p v-else class="text-xs text-green-600 dark:text-green-400">
                                                Sem lar
                                            </p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <Badge :variant="user.role === 'admin' ? 'default' : 'secondary'" class="text-xs">
                                                    {{ user.role === 'admin' ? 'Admin' : 'Usuário' }}
                                                </Badge>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <div class="flex gap-2">
                                        <Button 
                                            size="sm"
                                            @click="addMember(user)"
                                            class="flex-1"
                                        >
                                            <UserPlus class="w-4 h-4 mr-1" />
                                            Adicionar
                                        </Button>
                                        <Button 
                                            variant="outline" 
                                            size="sm"
                                            @click="changeRole(user)"
                                        >
                                            <Shield class="w-4 h-4" />
                                        </Button>
                                        <Button 
                                            variant="destructive" 
                                            size="sm"
                                            @click="deleteUser(user)"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação -->
        <Dialog v-model:open="showConfirmModal">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ confirmAction.title }}</DialogTitle>
                </DialogHeader>
                
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ confirmAction.message }}
                </p>

                <DialogFooter>
                    <Button variant="outline" @click="showConfirmModal = false">
                        Cancelar
                    </Button>
                    <Button
                        :variant="confirmAction.type === 'delete' ? 'destructive' : 'default'"
                        @click="executeAction"
                        :disabled="processing"
                    >
                        {{ confirmAction.confirmText }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
// usePage removido - não utilizado
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import { UserPlus, UserMinus, Users, Trash2, Shield } from 'lucide-vue-next'
import { create as userManagementCreate } from '@/routes/user-management'

interface User {
    id: number
    name: string
    email: string
    role: 'admin' | 'user'
    household?: { name: string }
}

interface Household {
    id: number
    name: string
    created_by: number
}

const props = defineProps<{
    household: Household
    members: User[]
    availableUsers: User[]
}>()

const showConfirmModal = ref(false)
const processing = ref(false)
const confirmAction = ref({
    title: '',
    message: '',
    confirmText: '',
    type: 'default',
    action: () => {}
})

const addMember = (user: User) => {
    confirmAction.value = {
        title: 'Adicionar Membro',
        message: `Deseja adicionar ${user.name} ao lar "${props.household.name}"?`,
        confirmText: 'Adicionar',
        type: 'default',
        action: () => {
            router.post(`/user-management/${user.id}/add-to-household`, {}, {
                onStart: () => processing.value = true,
                onFinish: () => {
                    processing.value = false
                    showConfirmModal.value = false
                }
            })
        }
    }
    showConfirmModal.value = true
}

const removeMember = (user: User) => {
    confirmAction.value = {
        title: 'Remover Membro',
        message: `Deseja remover ${user.name} do lar "${props.household.name}"?`,
        confirmText: 'Remover',
        type: 'default',
        action: () => {
            router.post(`/user-management/${user.id}/remove-from-household`, {}, {
                onStart: () => processing.value = true,
                onFinish: () => {
                    processing.value = false
                    showConfirmModal.value = false
                }
            })
        }
    }
    showConfirmModal.value = true
}

const deleteUser = (user: User) => {
    confirmAction.value = {
        title: 'Excluir Usuário',
        message: `Deseja excluir permanentemente o usuário ${user.name}? Esta ação não pode ser desfeita.`,
        confirmText: 'Excluir',
        type: 'delete',
        action: () => {
            router.delete(`/user-management/${user.id}`, {
                onStart: () => processing.value = true,
                onFinish: () => {
                    processing.value = false
                    showConfirmModal.value = false
                }
            })
        }
    }
    showConfirmModal.value = true
}

const changeRole = (user: User) => {
    const newRole = user.role === 'admin' ? 'user' : 'admin'
    const roleNames = {
        admin: 'Administrador',
        user: 'Usuário'
    }
    
    confirmAction.value = {
        title: 'Alterar Role',
        message: `Deseja alterar a role de ${user.name} para ${roleNames[newRole]}?`,
        confirmText: 'Alterar',
        type: 'default',
        action: () => {
            router.patch(`/user-management/${user.id}/role`, { role: newRole }, {
                onStart: () => processing.value = true,
                onFinish: () => {
                    processing.value = false
                    showConfirmModal.value = false
                }
            })
        }
    }
    showConfirmModal.value = true
}

const executeAction = () => {
    confirmAction.value.action()
}
</script>
