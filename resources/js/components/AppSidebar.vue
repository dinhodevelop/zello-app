<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { dashboard } from '../routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {  LayoutGrid, TrendingUp, TrendingDown, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => (page.props as any).auth?.user);

const mainNavItems = computed((): NavItem[] => {
    const baseItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Receitas',
            href: '/receitas',
            icon: TrendingUp,
        },
        {
            title: 'Despesas',
            href: '/despesas',
            icon: TrendingDown,
        },
    ];

    // Apenas admins podem ver o gerenciamento de usuários
    if (user.value?.role === 'admin') {
        baseItems.push({
            title: 'Gerenciar Usuários',
            href: '/user-management',
            icon: Users,
        });
    }

    return baseItems;
});

const footerNavItems: NavItem[] = [

];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
