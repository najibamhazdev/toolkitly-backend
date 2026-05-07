<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    metadataUrl: {
        type: String,
        required: true,
    },
    mergeUrl: {
        type: String,
        required: true,
    },
});

const files = ref([]);
const status = ref('');
const isMerging = ref(false);
const result = ref(null);
const limits = ref({
    min_files: 2,
    max_files: 20,
    max_upload_kb: 10240,
    expires_after_seconds: 3600,
});

const canMerge = computed(() => files.value.length >= limits.value.min_files && !isMerging.value);

const maxUploadMb = computed(() => Math.round((limits.value.max_upload_kb / 1024) * 10) / 10);

function setStatus(message) {
    status.value = message;
}

function formatSize(bytes) {
    if (bytes < 1024 * 1024) {
        return `${Math.max(1, Math.round(bytes / 1024)).toLocaleString()} KB`;
    }

    return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
}

function addFiles(selectedFiles) {
    const incoming = Array.from(selectedFiles || []);
    const pdfs = incoming.filter((file) => file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'));
    const availableSlots = limits.value.max_files - files.value.length;

    files.value = [
        ...files.value,
        ...pdfs.slice(0, Math.max(0, availableSlots)).map((file) => ({
            id: crypto.randomUUID(),
            file,
        })),
    ];

    result.value = null;

    if (pdfs.length !== incoming.length) {
        setStatus('Only PDF files were added.');
        return;
    }

    if (pdfs.length > availableSlots) {
        setStatus(`Only ${limits.value.max_files} PDFs can be merged at once.`);
        return;
    }

    setStatus(`${files.value.length} PDF${files.value.length === 1 ? '' : 's'} ready.`);
}

function handleDrop(event) {
    event.preventDefault();
    addFiles(event.dataTransfer.files);
}

function removeFile(id) {
    files.value = files.value.filter((entry) => entry.id !== id);
    result.value = null;
    setStatus(`${files.value.length} PDF${files.value.length === 1 ? '' : 's'} ready.`);
}

function moveFile(index, direction) {
    const nextIndex = index + direction;

    if (nextIndex < 0 || nextIndex >= files.value.length) {
        return;
    }

    const reordered = [...files.value];
    const [entry] = reordered.splice(index, 1);
    reordered.splice(nextIndex, 0, entry);
    files.value = reordered;
    result.value = null;
}

async function loadMetadata() {
    try {
        const response = await fetch(props.metadataUrl, {
            headers: { Accept: 'application/json' },
        });

        if (response.ok) {
            limits.value = await response.json();
        }
    } catch {
        setStatus('Using local PDF merge settings.');
    }
}

async function mergePdf() {
    if (files.value.length < limits.value.min_files) {
        setStatus('Add at least two PDF files.');
        return;
    }

    isMerging.value = true;
    result.value = null;
    setStatus('Merging PDFs...');

    const formData = new FormData();
    files.value.forEach((entry) => formData.append('files[]', entry.file));

    try {
        const response = await fetch(props.mergeUrl, {
            method: 'POST',
            headers: { Accept: 'application/json' },
            body: formData,
        });
        const data = await response.json();

        if (!response.ok) {
            const message = data.message || data.errors?.files?.[0] || 'These PDFs could not be merged.';
            throw new Error(message);
        }

        result.value = data.file;
        setStatus('Merged PDF ready.');
    } catch (error) {
        setStatus(error.message || 'These PDFs could not be merged.');
    } finally {
        isMerging.value = false;
    }
}

onMounted(loadMetadata);
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">Free server-side tool</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">Merge PDF</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">
                    Combine multiple PDFs into one file. Arrange the order, merge, and download the result.
                </p>
            </div>

            <div
                class="grid min-h-48 place-items-center rounded-lg border border-dashed border-[#9ab2a8] bg-white px-4 py-8 text-center shadow-sm transition hover:border-[#2f7c67]"
                @dragover.prevent
                @drop="handleDrop"
            >
                <div>
                    <p class="text-base font-semibold text-[#171411]">Drop PDF files here</p>
                    <p class="mt-2 text-sm text-[#655d51]">Up to {{ limits.max_files }} files, {{ maxUploadMb }} MB each.</p>
                    <label class="mt-5 inline-flex cursor-pointer rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus-within:ring-4 focus-within:ring-[#101820]/20">
                        Choose PDFs
                        <input type="file" accept="application/pdf,.pdf" multiple class="sr-only" @change="addFiles($event.target.files); $event.target.value = ''">
                    </label>
                </div>
            </div>

            <div v-if="files.length" class="mt-5 grid gap-3">
                <div
                    v-for="(entry, index) in files"
                    :key="entry.id"
                    class="grid gap-3 rounded-lg border border-[#cdd8d2] bg-white p-3 shadow-sm sm:grid-cols-[auto_minmax(0,1fr)_auto]"
                >
                    <div class="grid size-10 place-items-center rounded-lg bg-[#e8f0ec] text-sm font-bold text-[#2f7c67]">
                        {{ index + 1 }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-[#171411]">{{ entry.file.name }}</p>
                        <p class="text-sm text-[#655d51]">{{ formatSize(entry.file.size) }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="rounded-lg border border-[#c6d4cd] px-3 py-2 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] disabled:opacity-40" :disabled="index === 0" @click="moveFile(index, -1)">
                            Up
                        </button>
                        <button type="button" class="rounded-lg border border-[#c6d4cd] px-3 py-2 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] disabled:opacity-40" :disabled="index === files.length - 1" @click="moveFile(index, 1)">
                            Down
                        </button>
                        <button type="button" class="rounded-lg border border-[#c6d4cd] px-3 py-2 text-sm font-semibold text-[#171411] transition hover:border-[#9b4c4c] hover:bg-[#fff6f6]" @click="removeFile(entry.id)">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                <button
                    type="button"
                    class="w-full rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus:outline-none focus:ring-4 focus:ring-[#101820]/20 disabled:cursor-not-allowed disabled:opacity-45"
                    :disabled="!canMerge"
                    @click="mergePdf"
                >
                    {{ isMerging ? 'Merging...' : 'Merge PDFs' }}
                </button>

                <div v-if="result" class="mt-4 rounded-lg border border-[#cdd8d2] bg-[#f8faf8] p-4">
                    <p class="text-sm font-semibold text-[#171411]">Merged file ready</p>
                    <p class="mt-1 text-sm text-[#655d51]">{{ formatSize(result.size) }} · deletes automatically in about 1 hour</p>
                    <a :href="result.download_url" class="mt-4 inline-flex w-full justify-center rounded-lg border border-[#2f7c67] bg-[#2f7c67] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#286b59]">
                        Download PDF
                    </a>
                </div>

                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
