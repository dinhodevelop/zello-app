<template>
    <AppLayout title="Lares Disponíveis">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Lares Disponíveis
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Informações -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                Escolha um Lar Familiar
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Selecione um lar existente para participar ou crie um novo.
                            </p>
                        </div>

                        <!-- Lista de Lares -->
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div 
                                v-for="household in households" 
                                :key="household.id"
                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            >
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                            {{ household.name }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ household.users.length }} {{ household.users.length === 1 ? 'membro' : 'membros' }}
                                        </p>
                                    </div>
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <Home class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Membros:</p>
                                    <div class="flex flex-wrap gap-1">
                                        <span 
                                            v-for="user in household.users.slice(0, 3)" 
                                            :key="user.id"
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300"
                                        >
                                            {{ user.name }}
                                        </span>
                                        <span 
                                            v-if="household.users.length > 3"
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300"
                                        >
                                            +{{ household.users.length - 3 }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <Button 
                                        size="sm" 
                                        class="flex-1"
                                        @click="joinHousehold(household.id)"
                                        :disabled="isCurrentHousehold(household.id)"
                                    >
                                        <span v-if="isCurrentHousehold(household.id)">Lar Atual</span>
                                        <span v-else>Entrar</span>
                                    </Button>
                                    <Button 
                                        variant="outline" 
                                        size="sm"
                                        @click="viewHousehold(household.id)"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Criar Novo Lar -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-center">
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Não encontrou o lar que procura?
                                </p>
                                <Button as-child>
                                    <Link :href="householdCreate().url">
                                        <Plus class="w-4 h-4 mr-2" />
                                        Criar Novo Lar
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useForm, usePage, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Home, Eye, Plus } from 'lucide-vue-next'
import { create as householdCreate, join as householdJoin, show as householdShow } from '@/routes/household'

interface Household {
    id: number
    name: string
    users: Array<{ id: number; name: string }>
}

const props = defineProps<{
    households: Household[]
}>()

const page = usePage()
const user = computed(() => page.props.auth.user)
const currentHouseholdId = computed(() => user.value?.household?.id)

const isCurrentHousehold = (householdId: number) => {
    return currentHouseholdId.value === householdId
}

const joinHousehold = (householdId: number) => {
    if (!isCurrentHousehold(householdId)) {
        router.post(householdJoin(householdId).url)
    }
}

const viewHousehold = (householdId: number) => {
    router.visit(householdShow(householdId).url)
}
</script>
