<script setup>
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="ColabDoc" />

    <div
        class="min-h-screen bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-white"
    >
        <!-- Navbar -->
        <header
            class="border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900"
        >
            <div
                class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center text-white"
                    >
                        📄
                    </div>

                    <div>
                        <h1 class="font-bold text-xl">ColabDoc</h1>

                        <p class="text-sm text-slate-500">
                            Collaborative Workspace
                        </p>
                    </div>
                </div>

                <!-- LOGIN AREA -->
                <div v-if="canLogin" class="flex items-center gap-3">
                    <template v-if="$page.props.auth.user">
                        <!-- LOGIC TETAP -->
                        <Link
                            :href="route('dashboard')"
                            class="px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white transition"
                        >
                            Open Dashboard
                        </Link>
                    </template>

                    <template v-else>
                        <!-- LOGIC TETAP -->
                        <Link
                            :href="route('login')"
                            class="px-5 py-2.5 rounded-2xl hover:bg-slate-200 dark:hover:bg-slate-800 transition"
                        >
                            Login
                        </Link>

                        <!-- LOGIC TETAP -->
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white transition"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <main
            class="max-w-7xl mx-auto px-6 py-24 grid lg:grid-cols-2 gap-16 items-center"
        >
            <!-- Left -->
            <div>
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-sm font-medium mb-8"
                >
                    ✨ Real-time Collaborative Editor
                </div>

                <h1
                    class="text-5xl lg:text-7xl font-black leading-tight tracking-tight"
                >
                    Write Together
                    <span class="text-indigo-600"> in Real-Time </span>
                </h1>

                <p
                    class="mt-8 text-lg text-slate-500 leading-relaxed max-w-2xl"
                >
                    Create documents, collaborate live with your team, and
                    manage revisions seamlessly using ColabDoc.
                </p>

                <!-- CTA -->
                <div class="mt-10 flex flex-wrap gap-4">
                    <template v-if="$page.props.auth.user">
                        <!-- LOGIC TETAP -->
                        <Link
                            :href="route('dashboard')"
                            class="px-8 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-xl transition"
                        >
                            Open Workspace →
                        </Link>
                    </template>

                    <template v-else>
                        <!-- LOGIC TETAP -->
                        <Link
                            :href="route('login')"
                            class="px-8 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-xl transition"
                        >
                            Start Writing
                        </Link>

                        <!-- LOGIC TETAP -->
                        <Link
                            :href="route('register')"
                            class="px-8 py-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold transition"
                        >
                            Create Account
                        </Link>
                    </template>
                </div>
            </div>

            <!-- Right -->
            <div
                class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden"
            >
                <!-- Top -->
                <div
                    class="h-16 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>

                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>

                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                    </div>

                    <div class="text-sm text-slate-500">
                        realtime-document.md
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8 space-y-5">
                    <div
                        class="h-5 w-52 rounded bg-slate-200 dark:bg-slate-800"
                    ></div>

                    <div class="space-y-3">
                        <div
                            class="h-4 w-full rounded bg-slate-100 dark:bg-slate-800"
                        ></div>

                        <div
                            class="h-4 w-11/12 rounded bg-slate-100 dark:bg-slate-800"
                        ></div>

                        <div
                            class="h-4 w-10/12 rounded bg-slate-100 dark:bg-slate-800"
                        ></div>
                    </div>

                    <!-- Collaboration -->
                    <div class="flex items-center justify-between pt-8">
                        <div class="flex -space-x-3">
                            <div
                                class="w-10 h-10 rounded-full bg-indigo-500 border-4 border-white dark:border-slate-900"
                            ></div>

                            <div
                                class="w-10 h-10 rounded-full bg-pink-500 border-4 border-white dark:border-slate-900"
                            ></div>

                            <div
                                class="w-10 h-10 rounded-full bg-emerald-500 border-4 border-white dark:border-slate-900"
                            ></div>
                        </div>

                        <div class="text-sm text-emerald-500 font-medium">
                            ● Live Collaboration
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
