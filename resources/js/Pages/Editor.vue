```vue
<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useEditor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import { router } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    document: Object,
});

const showHistory = ref(false);

const activeUsers = ref([]);

const isTyping = ref(false);

const title = ref(props.document.title);

let saveTimeout = null;
let titleTimeout = null;

/*
|--------------------------------------------------------------------------
| GOOGLE DOCS STYLE TITLE UPDATE
|--------------------------------------------------------------------------
*/
const updateTitle = () => {
    clearTimeout(titleTimeout);

    titleTimeout = setTimeout(() => {
        axios.put(route("documents.update", props.document.slug), {
            title: title.value,
        });
    }, 800);
};

const restoreVersion = (revisionId) => {
    if (
        confirm(
            "Apakah Anda yakin ingin mengembalikan ke versi ini? Konten saat ini akan diganti.",
        )
    ) {
        router.post(
            route("documents.restore", [props.document.slug, revisionId]),
            {},
            {
                onSuccess: () => {
                    editor.value.commands.setContent(props.document.content);

                    showHistory.value = false;
                },
            },
        );
    }
};

const saveDocument = (content) => {
    axios
        .put(route("documents.update", props.document.slug), {
            content: content,
        })
        .then(() => {
            console.log("Dokumen berhasil di-autosave");
        });
};

// TIPTAP
const editor = useEditor({
    extensions: [StarterKit],

    content: props.document.content || "<p>Mulai mengetik di sini...</p>",

    editorProps: {
        attributes: {
            class: "focus:outline-none min-h-[700px] text-slate-800",
        },
    },

    onUpdate: ({ editor }) => {
        const content = editor.getHTML();

        /*
        |--------------------------------------------------------------------------
        | REALTIME EXISTING LOGIC
        |--------------------------------------------------------------------------
        */
        window.Echo?.join(`document.${props.document.id}`).whisper("typing", {
            content: content,
        });

        /*
        |--------------------------------------------------------------------------
        | AUTOSAVE EXISTING LOGIC
        |--------------------------------------------------------------------------
        */
        clearTimeout(saveTimeout);

        saveTimeout = setTimeout(() => {
            saveDocument(content);
        }, 3000);
    },
});

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
                }, 2000);

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

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy();
    }

    window.Echo.leave(`document.${props.document.id}`);
});

const deleteAllHistory = () => {
    if (confirm("Apakah Anda yakin ingin menghapus SEMUA riwayat versi?")) {
        router.delete(route("documents.history.destroy", props.document.slug), {
            onSuccess: () => {
                alert("Semua riwayat versi telah dibersihkan.");
            },
        });
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-100">
        <!-- TOPBAR -->
        <div
            class="sticky top-0 z-50 bg-white border-b border-slate-200 px-8 py-5"
        >
            <div
                class="max-w-7xl mx-auto flex justify-between items-center gap-8"
            >
                <!-- LEFT -->
                <div class="flex-1">
                    <!-- TITLE -->
                    <input
                        v-model="title"
                        @input="updateTitle"
                        type="text"
                        placeholder="Untitled Document"
                        class="w-full text-4xl font-bold bg-transparent border-none outline-none focus:ring-0 text-slate-800 placeholder:text-slate-400"
                    />

                    <!-- STATUS -->
                    <div class="flex items-center gap-4 mt-2">
                        <p class="text-xs text-slate-500">Autosaved</p>

                        <p
                            v-if="isTyping"
                            class="text-xs text-blue-500 animate-pulse"
                        >
                            Someone is typing...
                        </p>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="flex items-center gap-4">
                    <!-- USERS -->
                    <div class="flex -space-x-3">
                        <div
                            v-for="user in activeUsers"
                            :key="user.id"
                            class="w-10 h-10 rounded-full bg-indigo-600 border-4 border-white flex items-center justify-center text-white text-sm font-bold uppercase shadow"
                            :title="user.name"
                        >
                            {{ user.name.charAt(0) }}
                        </div>
                    </div>

                    <!-- HISTORY -->
                    <button
                        @click="showHistory = !showHistory"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 transition text-sm font-medium"
                    >
                        {{ showHistory ? "Tutup History" : "Lihat History" }}
                    </button>
                </div>
            </div>
        </div>

        <!-- BODY -->
        <div class="max-w-7xl mx-auto py-10 px-4 flex gap-6">
            <!-- EDITOR -->
            <div class="flex-1">
                <!-- DOCUMENT -->
                <div
                    class="bg-white border border-slate-200 rounded-[32px] shadow-sm min-h-[800px] p-16"
                >
                    <editor-content :editor="editor" />
                </div>
            </div>

            <!-- HISTORY -->
            <div
                v-if="showHistory"
                class="w-72 border border-slate-200 bg-white p-5 rounded-3xl shadow-sm h-fit"
            >
                <div
                    class="flex justify-between items-center mb-4 border-b border-slate-200 pb-3"
                >
                    <h2 class="font-bold text-slate-700">Riwayat Versi</h2>

                    <button
                        v-if="
                            document.revisions && document.revisions.length > 0
                        "
                        @click="deleteAllHistory"
                        class="text-[10px] bg-red-100 hover:bg-red-200 text-red-600 px-2 py-1 rounded font-semibold transition"
                    >
                        Hapus Semua
                    </button>
                </div>

                <div
                    v-if="
                        !document.revisions || document.revisions.length === 0
                    "
                    class="text-sm text-slate-500 italic"
                >
                    Belum ada riwayat perubahan.
                </div>

                <div v-else class="space-y-4 overflow-y-auto max-h-[600px]">
                    <div
                        v-for="rev in document.revisions"
                        :key="rev.id"
                        class="p-3 border border-slate-200 rounded-2xl bg-white text-xs"
                    >
                        <p class="font-semibold text-indigo-600">
                            {{ rev.user?.name || "Anonim" }}
                        </p>

                        <p class="text-slate-500 mt-1">
                            {{
                                new Date(rev.created_at).toLocaleString("id-ID")
                            }}
                        </p>

                        <button
                            @click="restoreVersion(rev.id)"
                            class="mt-3 text-indigo-600 hover:underline font-semibold"
                        >
                            Restore Versi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.ProseMirror {
    outline: none;
    min-height: 700px;
    font-size: 18px;
    line-height: 1.9;
    color: #1e293b;
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
```
