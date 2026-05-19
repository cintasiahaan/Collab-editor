<script setup>
import { Link } from "@inertiajs/vue3";
import AppSidebar from "@/Components/AppSidebar.vue";

defineProps({
    documents: Array,
});
</script>

<template>
    <div class="flex min-h-screen bg-slate-100 dark:bg-slate-950">
        <!-- Sidebar -->
        <AppSidebar />

        <!-- Main -->
        <main class="flex-1 p-8 overflow-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h1
                        class="text-4xl font-bold text-slate-800 dark:text-white"
                    >
                        Welcome Back 👋
                    </h1>

                    <p class="mt-2 text-slate-500">
                        Manage and collaborate with your documents.
                    </p>
                </div>

                <!-- JANGAN UBAH LOGIC -->
                <Link
                    :href="route('documents.store')"
                    method="post"
                    as="button"
                    class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium shadow-lg transition"
                >
                    + New Document
                </Link>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm"
                >
                    <p class="text-slate-500 text-sm">Total Documents</p>

                    <h2
                        class="text-4xl font-bold mt-3 text-slate-800 dark:text-white"
                    >
                        {{ documents.length }}
                    </h2>
                </div>

                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm"
                >
                    <p class="text-slate-500 text-sm">Collaboration</p>

                    <h2
                        class="text-4xl font-bold mt-3 text-slate-800 dark:text-white"
                    >
                        Active
                    </h2>
                </div>

                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm"
                >
                    <p class="text-slate-500 text-sm">Revisions</p>

                    <h2
                        class="text-4xl font-bold mt-3 text-slate-800 dark:text-white"
                    >
                        Live
                    </h2>
                </div>
            </div>

            <!-- Documents -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2
                        class="text-2xl font-semibold text-slate-800 dark:text-white"
                    >
                        Recent Documents
                    </h2>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"
                >
                    <div
                        v-for="doc in documents"
                        :key="doc._id"
                        class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <h3
                                    class="text-xl font-semibold text-slate-800 dark:text-white line-clamp-1"
                                >
                                    {{ doc.title }}
                                </h3>

                                <p class="text-sm text-slate-500 mt-2">
                                    Collaborative Document
                                </p>
                            </div>

                            <div
                                class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center"
                            >
                                📄
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-between">
                            <!-- OWNER -->
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-300"
                                ></div>

                                <div>
                                    <p
                                        class="text-sm font-medium text-slate-700 dark:text-slate-200"
                                    >
                                        {{ doc.user?.name || "Unknown" }}
                                    </p>

                                    <p class="text-xs text-slate-500">Owner</p>
                                </div>
                            </div>

                            <!-- ACTION -->
                            <div class="flex items-center gap-2">
                                <!-- OPEN -->
                                <Link
                                    :href="route('documents.show', doc.slug)"
                                    class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition text-sm"
                                >
                                    Open
                                </Link>

                                <!-- DELETE -->
                                <Link
                                    :href="route('documents.destroy', doc.slug)"
                                    method="delete"
                                    as="button"
                                    onclick="
                                        return confirm('Hapus dokumen ini?');
                                    "
                                    class="px-4 py-2 rounded-xl bg-red-100 hover:bg-red-200 text-red-600 transition text-sm font-medium"
                                >
                                    Delete
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
