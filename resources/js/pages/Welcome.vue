<script setup lang="ts">
import { dashboard, login, register } from '../routes';
import { Head, Link } from '@inertiajs/vue3';
import Icon from '../components/Icon.vue';
import { Coins, ListChecks, Home, CheckCircle } from 'lucide-vue-next';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="min-h-screen bg-gradient-to-br from-[#FDFDFC] via-[#F8F9FA] to-[#F1F3F4] dark:from-[#0a0a0a] dark:via-[#111111] dark:to-[#1a1a1a]">
        <!-- Navigation Header -->
        <header class="relative z-10 w-full px-6 py-4 lg:px-8">
            <nav class="mx-auto flex max-w-7xl items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <Icon name="home" class="h-8 w-8 text-green-600 dark:text-green-500" />
                    <span class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Zello</span>
                </div>
                
                <!-- Auth Links -->
                <div class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-flex items-center rounded-lg border border-[#19140035] bg-white px-6 py-2.5 text-sm font-medium text-[#1b1b18] shadow-sm transition-all hover:border-[#1915014a] hover:shadow-md dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        <Icon name="layout-dashboard" class="mr-2 h-4 w-4" />
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="inline-flex items-center rounded-lg px-6 py-2.5 text-sm font-medium text-[#1b1b18] transition-all hover:bg-[#f8f9fa] dark:text-[#EDEDEC] dark:hover:bg-[#1a1a1a]"
                        >
                            Entrar
                        </Link>
                        <Link
                            :href="register()"
                            class="inline-flex items-center rounded-lg bg-green-600 px-6 py-2.5 text-sm font-medium text-white shadow-lg transition-all hover:bg-green-700 hover:shadow-xl dark:bg-green-500 dark:hover:bg-green-600"
                        >
                            Cadastrar
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <section class="relative px-6 py-16 lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                    <!-- Content -->
                    <div class="space-y-8">
                        <div class="space-y-4">
                            <h1 class="text-4xl font-bold tracking-tight text-[#1b1b18] sm:text-5xl lg:text-6xl dark:text-[#EDEDEC]">
                                Organize sua 
                                <span class="bg-gradient-to-r from-green-600 to-emerald-500 bg-clip-text text-transparent dark:from-green-500 dark:to-emerald-400">
                                    vida familiar
                                </span> 
                                em um só lugar
                            </h1>
                            <p class="text-lg text-[#706f6c] sm:text-xl dark:text-[#A1A09A]">
                                Gerencie finanças, listas de compras, tarefas domésticas e metas familiares com facilidade. 
                                Mantenha toda a família conectada e organizada.
                            </p>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col gap-4 sm:flex-row sm:gap-6">
                            <Link
                                :href="register()"
                                class="inline-flex items-center justify-center rounded-lg bg-green-600 px-8 py-4 text-base font-semibold text-white shadow-lg transition-all hover:bg-green-700 hover:shadow-xl hover:scale-105 dark:bg-green-500 dark:hover:bg-green-600"
                            >
                                <Icon name="rocket" class="mr-2 h-5 w-5" />
                                Começar Gratuitamente
                            </Link>
                            <Link
                                :href="login()"
                                class="inline-flex items-center justify-center rounded-lg border-2 border-green-600 px-8 py-4 text-base font-semibold text-green-600 transition-all hover:bg-green-600 hover:text-white dark:border-green-500 dark:text-green-500 dark:hover:bg-green-500 dark:hover:text-white"
                            >
                                <Icon name="log-in" class="mr-2 h-5 w-5" />
                                Já tenho conta
                            </Link>
                        </div>

                        <!-- Trust Indicators -->
                        <div class="flex items-center gap-6 pt-4">
                            <div class="flex items-center gap-2">
                                <Icon name="shield-check" class="h-5 w-5 text-green-600" />
                                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">100% Seguro</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Icon name="users" class="h-5 w-5 text-blue-600" />
                                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Colaborativo</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Icon name="smartphone" class="h-5 w-5 text-purple-600" />
                                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Multiplataforma</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Image/Illustration -->
                    <div class="relative">
                        <div class="relative rounded-2xl bg-gradient-to-br from-green-600/10 to-emerald-500/10 p-8 shadow-2xl dark:from-green-500/10 dark:to-emerald-400/10">
                            <!-- Placeholder for family illustration -->
                            <div class="aspect-square rounded-xl bg-gradient-to-br from-white to-[#f8f9fa] p-8 shadow-inner dark:from-[#1a1a1a] dark:to-[#2a2a2a]">
                                <div class="flex h-full items-center justify-center">
                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Family icons representing organization -->
                                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-green-600/10 dark:bg-green-500/10">
                                            <Icon name="heart" class="h-8 w-8 text-green-600 dark:text-green-500" />
                                        </div>
                                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-blue-500/10">
                                            <Icon name="calendar" class="h-8 w-8 text-blue-600" />
                                        </div>
                                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-green-500/10">
                                            <Icon name="dollar-sign" class="h-8 w-8 text-green-600" />
                                        </div>
                                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-purple-500/10">
                                            <CheckCircle name="check-circle" class="h-8 w-8 text-purple-600" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Floating elements for visual appeal -->
                            <div class="absolute -top-4 -right-4 h-8 w-8 rounded-full bg-green-600 shadow-lg dark:bg-green-500"></div>
                            <div class="absolute -bottom-4 -left-4 h-6 w-6 rounded-full bg-emerald-500 shadow-lg"></div>
                            <div class="absolute top-1/2 -left-2 h-4 w-4 rounded-full bg-green-400 shadow-lg"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="relative px-6 py-16 lg:px-8 lg:py-20">
            <div class="mx-auto max-w-7xl">
                <!-- Section Header -->
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-[#1b1b18] sm:text-4xl dark:text-[#EDEDEC]">
                        Funcionalidades que 
                        <span class="bg-gradient-to-r from-green-600 to-emerald-500 bg-clip-text text-transparent dark:from-green-500 dark:to-emerald-400">
                            transformam
                        </span> 
                        sua família
                    </h2>
                    <p class="mt-4 text-lg text-[#706f6c] dark:text-[#A1A09A]">
                        Descubra como o Zello pode simplificar a organização da sua casa
                    </p>
                </div>

                <!-- Features Grid -->
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Card 1: Controle Financeiro -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white p-8 shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 dark:bg-[#161615]">
                        <!-- Background Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 opacity-50 dark:from-green-900/20 dark:to-emerald-900/20"></div>
                        
                        <!-- Content -->
                        <div class="relative">
                            <!-- Icon -->
                            <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-green-600 to-emerald-500 shadow-lg">
                                <Coins name="dollar-sign" class="h-8 w-8 text-white" />
                            </div>
                            
                            <!-- Title -->
                            <h3 class="mb-4 text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                                💰 Controle Financeiro
                            </h3>
                            
                            <!-- Description -->
                            <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">
                                Acompanhe receitas, despesas e metas de toda a família. Tenha transparência total sobre as finanças do lar e tome decisões inteligentes juntos.
                            </p>
                            
                            <!-- Features List -->
                            <ul class="space-y-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                <li class="flex items-center">
                                    <Icon name="check" class="mr-2 h-4 w-4 text-green-600" />
                                    Controle de receitas e despesas
                                </li>
                                <li class="flex items-center">
                                    <Icon name="check" class="mr-2 h-4 w-4 text-green-600" />
                                    Metas financeiras familiares
                                </li>
                                <li class="flex items-center">
                                    <Icon name="check" class="mr-2 h-4 w-4 text-green-600" />
                                    Relatórios detalhados
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Hover Effect -->
                        <div class="absolute inset-0 rounded-2xl border-2 border-transparent transition-all duration-300 group-hover:border-green-200 dark:group-hover:border-green-800"></div>
                    </div>

                    <!-- Card 2: Listas Inteligentes -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white p-8 shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 dark:bg-[#161615]">
                        <!-- Background Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-50 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
                        
                        <!-- Content -->
                        <div class="relative">
                            <!-- Icon -->
                            <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-500 shadow-lg">
                                <ListChecks name="list-checks" class="h-8 w-8 text-white" />
                            </div>
                            
                            <!-- Title -->
                            <h3 class="mb-4 text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                                📝 Listas Inteligentes
                            </h3>
                            
                            <!-- Description -->
                            <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">
                                Crie listas de compras colaborativas e nunca esqueça nada. Organize compras por categoria e compartilhe com toda a família.
                            </p>
                            
                            <!-- Features List -->
                            <ul class="space-y-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                <li class="flex items-center">
                                    <Icon name="check" class="mr-2 h-4 w-4 text-blue-600" />
                                    Listas colaborativas em tempo real
                                </li>
                                <li class="flex items-center">
                                    <Icon name="check" class="mr-2 h-4 w-4 text-blue-600" />
                                    Categorização automática
                                </li>
                                <li class="flex items-center">
                                    <Icon name="check" class="mr-2 h-4 w-4 text-blue-600" />
                                    Histórico de compras
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Hover Effect -->
                        <div class="absolute inset-0 rounded-2xl border-2 border-transparent transition-all duration-300 group-hover:border-blue-200 dark:group-hover:border-blue-800"></div>
                    </div>

                    <!-- Card 3: Gestão Doméstica -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white p-8 shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 dark:bg-[#161615] md:col-span-2 lg:col-span-1">
                        <!-- Background Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 opacity-50 dark:from-purple-900/20 dark:to-pink-900/20"></div>
                        
                        <!-- Content -->
                        <div class="relative">
                            <!-- Icon -->
                            <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-600 to-pink-500 shadow-lg">
                                <Home name="home" class="h-8 w-8 text-white" />
                            </div>
                            
                            <!-- Title -->
                            <h3 class="mb-4 text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                                🏠 Gestão Doméstica
                            </h3>
                            
                            <!-- Description -->
                            <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">
                                Organize tarefas da casa e demandas familiares. Distribua responsabilidades e mantenha a casa sempre em ordem.
                            </p>
                            
                            <!-- Features List -->
                            <ul class="space-y-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                <li class="flex items-center">
                                    <Icon name="check" class="mr-2 h-4 w-4 text-purple-600" />
                                    Distribuição de tarefas
                                </li>
                                <li class="flex items-center">
                                    <Icon name="check" class="mr-2 h-4 w-4 text-purple-600" />
                                    Calendário familiar
                                </li>
                                <li class="flex items-center">
                                    <Icon name="check" class="mr-2 h-4 w-4 text-purple-600" />
                                    Lembretes automáticos
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Hover Effect -->
                        <div class="absolute inset-0 rounded-2xl border-2 border-transparent transition-all duration-300 group-hover:border-purple-200 dark:group-hover:border-purple-800"></div>
                    </div>
                </div>

                <!-- Bottom CTA -->
                <div class="mt-16 text-center">
                    <p class="mb-6 text-lg text-[#706f6c] dark:text-[#A1A09A]">
                        Pronto para experimentar todas essas funcionalidades?
                    </p>
                    <Link
                        :href="register()"
                        class="inline-flex items-center justify-center rounded-lg bg-green-600 px-8 py-4 text-base font-semibold text-white shadow-lg transition-all hover:bg-green-700 hover:shadow-xl hover:scale-105 dark:bg-green-500 dark:hover:bg-green-600"
                    >
                        <Icon name="arrow-right" class="mr-2 h-5 w-5" />
                        Começar Agora
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>