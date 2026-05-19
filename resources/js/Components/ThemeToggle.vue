<script setup>
import { ref, onMounted } from "vue";

const dark = ref(false);

onMounted(() => {
    dark.value = localStorage.theme === "dark";

    if (dark.value) {
        document.documentElement.classList.add("dark");
    }
});

const toggleTheme = () => {
    dark.value = !dark.value;

    if (dark.value) {
        document.documentElement.classList.add("dark");
        localStorage.theme = "dark";
    } else {
        document.documentElement.classList.remove("dark");
        localStorage.theme = "light";
    }
};
</script>

<template>
    <button
        @click="toggleTheme"
        class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700"
    >
        {{ dark ? "☀️" : "🌙" }}
    </button>
</template>
