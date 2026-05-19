<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useEditor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    document: Object,
});

const title = ref(props.document.title);

const activeUsers = ref([]);

const showHistory = ref(false);

const isTyping = ref(false);

const saveStatus = ref("Saved");

let saveTimeout = null;
let titleTimeout = null;

/*
|--------------------------------------------------------------------------
| GOOGLE DOCS STYLE TITLE SAVE
|--------------------------------------------------------------------------
*/
const updateTitle = () => {
    clearTimeout(titleTimeout);

    saveStatus.value = "Saving title...";

    titleTimeout = setTimeout(() => {
        router.put(
            route("documents.update", props.document.slug),
            {
                title: title.value,
            },
            {
                preserveScroll: true,
                preserveState: true,

                onSuccess: () => {
                    saveStatus.value = "Saved";
                },
            },
        );
    }, 800);
};

/*
|--------------------------------------------------------------------------
| DOCUMENT SAVE
|--------------------------------------------------------------------------
*/
const saveDocument = (content) => {
    saveStatus.value = "Saving...";

    axios
        .put(route("documents.update", props.document.slug), {
            content: content,
        })
        .then(() => {
            saveStatus.value = "Saved";
        });
};

/*
|--------------------------------------------------------------------------
| RESTORE VERSION
|--------------------------------------------------------------------------
*/
const restoreVersion = (revisionId) => {
    if (confirm("Restore this version?")) {
        router.post(
            route("documents.restore", [props.document.slug, revisionId]),
            {},
            {
                onSuccess: () => {
                    location.reload();
                },
            },
        );
    }
};

/*
|--------------------------------------------------------------------------
| TIPTAP
|--------------------------------------------------------------------------
*/
const editor = useEditor({
    extensions: [StarterKit],

    content: props.document.content || "<p>Start writing...</p>",

    editorProps: {
        attributes: {
            class: "focus:outline-none min-h-[700px] text-slate-800 dark:text-slate-100",
        },
    },

    onUpdate: ({ editor }) => {
        const content = editor.getHTML();

        saveStatus.value = "Typing...";

        /*
        |--------------------------------------------------------------------------
        | REALTIME
        |--------------------------------------------------------------------------
        */
        window.Echo?.join(`document.${props.document.id}`).whisper("typing", {
            content,
        });

        /*
        |--------------------------------------------------------------------------
        | AUTOSAVE
        |--------------------------------------------------------------------------
        */
        clearTimeout(saveTimeout);

        saveTimeout = setTimeout(() => {
            saveDocument(content);
        }, 3000);
    },
});

/*
|--------------------------------------------------------------------------
| REALTIME PRESENCE
|--------------------------------------------------------------------------
*/
onMounted(() => {
    if (window.Echo) {
        window.Echo.join(`document.${props.document.id}`)

            .here((users) => {
                activeUsers.value = users;
            })

            .joining((user) => {
                activeUsers.value.push(user);
            })

            .leaving((user) => {
                activeUsers.value = activeUsers.value.filter(
                    (u) => u.id !== user.id,
                );
            })

            .listenForWhisper("typing", (e) => {
                isTyping.value = true;

                setTimeout(() => {
                    isTyping.value = false;
                }, 1500);

                if (editor.value && editor.value.getHTML() !== e.content) {
                    const { from, to } = editor.value.state.selection;

                    editor.value.commands.setContent(e.content, false);

                    editor.value.commands.setTextSelection({
                        from,
                        to,
                    });
                }
            });
    } else {
        console.error("Laravel Echo gagal dimuat.");
    }
});

/*
|--------------------------------------------------------------------------
| DESTROY
|--------------------------------------------------------------------------
*/
onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy();
    }

    window.Echo.leave(`document.${props.document.id}`);
});
</script>

<template>
    <div class="min-h-screen bg-slate-100 dark:bg-slate-950">
        <!-- TOP NAVBAR -->
        <header
            class="h-16 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 sticky top-0 z-50"
        >
            <div
                class="max-w-[1600px] mx-auto h-full px-6 flex items-center justify-between"
            >
                <!-- LEFT -->
                <div class="flex items-center gap-4 flex-1">
                    <!-- LOGO -->
                    <Link
                        :href="route('dashboard')"
                        class="w-10 h-10 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-md"
                    >
                        📄
                    </Link>

                    <!-- TITLE -->
                    <div class="flex-1">
                        <input
                            v-model="title"
                            @input="updateTitle"
                            class="w-full bg-transparent border-none outline-none focus:ring-0 text-2xl font-bold text-slate-800 dark:text-white"
                        />

                        <!-- STATUS -->
                        <div
                            class="flex items-center gap-3 text-xs text-slate-500 mt-1"
                        >
                            <span>
                                {{ saveStatus }}
                            </span>

                            <span v-if="isTyping"> Someone is typing... </span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="flex items-center gap-5">
                    <!-- USERS -->
                    <div class="flex -space-x-3">
                        <div
                            v-for="user in activeUsers"
                            :key="user.id"
                            class="w-10 h-10 rounded-full bg-indigo-600 border-4 border-white dark:border-slate-900 flex items-center justify-center text-white text-sm font-bold uppercase shadow"
                            :title="user.name"
                        >
                            {{ user.name.charAt(0) }}
                        </div>
                    </div>

                    <!-- HISTORY -->
                    <button
                        @click="showHistory = !showHistory"
                        class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition text-sm"
                    >
                        History
                    </button>
                </div>
            </div>
        </header>

        <!-- TOOLBAR -->
        <div
            class="sticky top-16 z-40 border-b border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 backdrop-blur"
        >
            <div
                class="max-w-[1600px] mx-auto px-6 py-3 flex items-center gap-2"
            >
                <button
                    @click="editor.chain().focus().toggleBold().run()"
                    class="px-4 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition font-bold"
                >
                    B
                </button>

                <button
                    @click="editor.chain().focus().toggleItalic().run()"
                    class="px-4 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition italic"
                >
                    I
                </button>

                <button
                    @click="editor.chain().focus().toggleBulletList().run()"
                    class="px-4 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                >
                    • List
                </button>

                <button
                    @click="
                        editor.chain().focus().toggleHeading({ level: 1 }).run()
                    "
                    class="px-4 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                >
                    H1
                </button>
            </div>
        </div>

        <!-- BODY -->
        <div class="max-w-[1600px] mx-auto flex gap-6 p-6">
            <!-- EDITOR -->
            <div class="flex-1">
                <div
                    class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[32px] shadow-sm overflow-hidden"
                >
                    <!-- DOCUMENT -->
                    <div class="max-w-4xl mx-auto p-16">
                        <EditorContent :editor="editor" />
                    </div>
                </div>
            </div>

            <!-- HISTORY -->
            <aside
                v-if="showHistory"
                class="w-80 shrink-0 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm h-fit sticky top-36"
            >
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-bold text-lg">Version History</h2>
                </div>

                <div
                    v-if="!document.revisions?.length"
                    class="text-sm text-slate-500"
                >
                    No history yet.
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="rev in document.revisions"
                        :key="rev.id"
                        class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800"
                    >
                        <p class="font-semibold">
                            {{ rev.user?.name || "Unknown" }}
                        </p>

                        <p class="text-xs text-slate-500 mt-1">
                            {{
                                new Date(rev.created_at).toLocaleString("id-ID")
                            }}
                        </p>

                        <button
                            @click="restoreVersion(rev.id)"
                            class="mt-3 text-indigo-600 hover:underline text-sm"
                        >
                            Restore Version
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</template>

<style>
.ProseMirror {
    outline: none;
    min-height: 700px;
    font-size: 18px;
    line-height: 1.9;
}

.ProseMirror h1 {
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 1rem;
}

.ProseMirror ul {
    padding-left: 1.5rem;
    list-style: disc;
}

.ProseMirror p {
    margin-bottom: 1rem;
}
</style>
