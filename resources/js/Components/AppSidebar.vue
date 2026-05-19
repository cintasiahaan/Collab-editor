<script setup>
import { Link } from "@inertiajs/vue3";

import { LayoutDashboard, FileText, Settings } from "lucide-vue-next";
</script>

<template>
    <aside
        class="w-72 h-screen bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col"
    >
        <!-- Logo -->
        <div
            class="h-20 flex items-center px-6 border-b border-slate-200 dark:border-slate-800"
        >
            <div class="flex items-center gap-3">
                <div
                    class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center text-white"
                >
                    📄
                </div>

                <div>
                    <h1
                        class="text-xl font-bold text-slate-800 dark:text-white"
                    >
                        ColabDoc
                    </h1>

                    <p class="text-sm text-slate-500">Workspace</p>
                </div>
            </div>
        </div>

        <!-- Menu -->
        <nav class="flex-1 p-4 space-y-2">
            <!-- Dashboard -->
            <Link
                :href="route('dashboard')"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            >
                <LayoutDashboard class="w-5 h-5" />

                Dashboard
            </Link>

            <!-- Documents -->
            <Link
                :href="route('dashboard')"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            >
                <FileText class="w-5 h-5" />

                Documents
            </Link>

            <!-- Settings -->
            <Link
                :href="route('profile.edit')"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            >
                <Settings class="w-5 h-5" />

                Settings
            </Link>
        </nav>
    </aside>
</template>
