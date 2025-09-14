<template>
    <div class="flex items-center gap-2">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button 
                    variant="ghost" 
                    size="sm" 
                    class="flex items-center gap-2 text-sm"
                    :class="{ 'text-orange-600 dark:text-orange-400': !currentHousehold }"
                >
                    <Home class="h-4 w-4" />
                    <span class="font-medium">{{ currentHousehold?.name || 'Selecionar Lar' }}</span>
                    <ChevronDown class="h-3 w-3" />
                </Button>
            </DropdownMenuTrigger>
            
            <DropdownMenuContent align="end" class="w-56">
                <template v-if="currentHousehold">
                    <DropdownMenuLabel>Lar Atual</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    
                    <DropdownMenuItem 
                        @click="switchHousehold(currentHousehold.id)"
                        class="flex items-center justify-between"
                    >
                        <div class="flex items-center gap-2">
                            <Home class="h-4 w-4" />
                            <span>{{ currentHousehold.name }}</span>
                        </div>
                        <Check class="h-4 w-4" />
                    </DropdownMenuItem>
                    
                    <DropdownMenuSeparator />
                </template>
                
                <template v-else>
                    <DropdownMenuLabel class="text-orange-600 dark:text-orange-400">
                        Nenhum Lar Selecionado
                    </DropdownMenuLabel>
                    <DropdownMenuSeparator />
                </template>
                
                <DropdownMenuItem as-child>
                    <Link :href="householdAvailable().url" class="flex items-center gap-2">
                        <Users class="h-4 w-4" />
                        <span>Procurar Lares</span>
                    </Link>
                </DropdownMenuItem>
                
                <DropdownMenuItem as-child>
                    <Link :href="householdCreate().url" class="flex items-center gap-2">
                        <Plus class="h-4 w-4" />
                        <span>Criar Novo Lar</span>
                    </Link>
                </DropdownMenuItem>
                
                <template v-if="currentHousehold && user.value?.role === 'admin'">
                    <DropdownMenuSeparator />
                    
                    <DropdownMenuItem as-child>
                        <Link :href="userManagementIndex().url" class="flex items-center gap-2">
                            <UserPlus class="h-4 w-4" />
                            <span>Gerenciar Usuários</span>
                        </Link>
                    </DropdownMenuItem>
                    
                    <DropdownMenuItem as-child>
                        <Link :href="householdIndex().url" class="flex items-center gap-2">
                            <Settings class="h-4 w-4" />
                            <span>Configurações do Lar</span>
                        </Link>
                    </DropdownMenuItem>
                </template>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '../components/ui/dropdown-menu'
import { Button } from '../components/ui/button'
import { Home, ChevronDown, Check, Plus, Settings, Users, UserPlus } from 'lucide-vue-next'
import { index as householdIndex, create as householdCreate, available as householdAvailable } from '../routes/household'
import { index as userManagementIndex } from '../routes/user-management'

const page = usePage()
const user = computed(() => (page.props.auth as any)?.user)
const currentHousehold = computed(() => user.value?.household)

// Por enquanto, mostra apenas o lar atual
// A funcionalidade completa de múltiplos lares pode ser expandida depois
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const availableHouseholds = computed(() => {
    if (currentHousehold.value) {
        return [currentHousehold.value]
    }
    return []
})

const switchHousehold = (householdId: number) => {
    if (householdId !== currentHousehold.value?.id) {
        router.post(`/household/${householdId}/switch`)
    }
}
</script>
