<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { recordToolEvent } from '../analytics';

const props = defineProps({
    metadataUrl: {
        type: String,
        required: true,
    },
    actionUrl: {
        type: String,
        required: true,
    },
    toolKind: {
        type: String,
        required: true,
    },
});

const files = ref([]);
const status = ref('');
const isProcessing = ref(false);
const result = ref(null);
const settings = reactive({
    pages: '1',
    resolution: 150,
    quality: 90,
    compression: 'ebook',
    angle: 90,
    password: '',
});
const limits = ref({
    title: 'PDF Tool',
    description: 'Process PDF files online.',
    accept: 'application/pdf,.pdf',
    max_files: 1,
    max_upload_kb: 10240,
    expires_after_seconds: 3600,
    exports: ['pdf'],
    defaults: {},
});

const maxUploadMb = computed(() => Math.round((limits.value.max_upload_kb / 1024) * 10) / 10);
const allowsMultiple = computed(() => limits.value.max_files > 1);
const canProcess = computed(() => files.value.length > 0 && !isProcessing.value);
const uploadLabel = computed(() => props.toolKind === 'jpg-to-pdf' ? 'Choose JPG images' : 'Choose file');
const dropLabel = computed(() => props.toolKind === 'jpg-to-pdf' ? 'Drop JPG images here' : 'Drop PDF file here');
const actionLabel = computed(() => ({
    'split-pdf': 'Split PDF',
    'pdf-to-jpg': 'Convert to JPG',
    'jpg-to-pdf': 'Create PDF',
    'remove-pdf-pages': 'Remove pages',
    'compress-pdf': 'Compress PDF',
    'rotate-pdf': 'Rotate PDF',
    'protect-pdf': 'Protect PDF',
    'unlock-pdf': 'Unlock PDF',
})[props.toolKind] || 'Process file');

function setStatus(message) {
    status.value = message;
}

function formatSize(bytes) {
    if (!bytes) {
        return '0 KB';
    }

    if (bytes < 1024 * 1024) {
        return `${Math.max(1, Math.round(bytes / 1024)).toLocaleString()} KB`;
    }

    return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
}

function acceptedFile(file) {
    if (props.toolKind === 'jpg-to-pdf') {
        return file.type === 'image/jpeg' || /\.(jpe?g)$/i.test(file.name);
    }

    return file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
}

function addFiles(selectedFiles) {
    const incoming = Array.from(selectedFiles || []);
    const maxBytes = limits.value.max_upload_kb * 1024;
    const valid = incoming.filter((file) => acceptedFile(file) && file.size <= maxBytes);
    const availableSlots = limits.value.max_files - files.value.length;
    const nextFiles = valid.slice(0, Math.max(0, availableSlots)).map((file) => ({
        id: crypto.randomUUID(),
        file,
    }));

    files.value = allowsMultiple.value ? [...files.value, ...nextFiles] : nextFiles.slice(0, 1);
    result.value = null;

    if (valid.length !== incoming.length) {
        setStatus(`Only supported files up to ${maxUploadMb.value} MB were added.`);
        return;
    }

    if (valid.length > availableSlots && allowsMultiple.value) {
        setStatus(`Only ${limits.value.max_files} files can be processed at once.`);
        return;
    }

    setStatus(`${files.value.length} file${files.value.length === 1 ? '' : 's'} ready.`);
}

function handleDrop(event) {
    event.preventDefault();
    addFiles(event.dataTransfer.files);
}

function removeFile(id) {
    files.value = files.value.filter((entry) => entry.id !== id);
    result.value = null;
    setStatus(`${files.value.length} file${files.value.length === 1 ? '' : 's'} ready.`);
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

        if (!response.ok) {
            return;
        }

        const data = await response.json();
        limits.value = data;
        settings.pages = data.defaults?.pages || settings.pages;
        settings.resolution = data.defaults?.resolution || settings.resolution;
        settings.quality = data.defaults?.quality || settings.quality;
        settings.compression = data.defaults?.quality || settings.compression;
        settings.angle = data.defaults?.angle || settings.angle;
        settings.password = data.defaults?.password || settings.password;
    } catch {
        setStatus('Using local PDF settings.');
    }
}

async function processFiles() {
    if (!files.value.length) {
        setStatus('Choose a file before processing.');
        return;
    }

    isProcessing.value = true;
    result.value = null;
    setStatus('Processing file...');

    const formData = new FormData();

    if (props.toolKind === 'jpg-to-pdf') {
        files.value.forEach((entry) => formData.append('files[]', entry.file));
    } else {
        formData.append('file', files.value[0].file);
    }

    if (props.toolKind === 'remove-pdf-pages') {
        formData.append('pages', settings.pages);
    }

    if (props.toolKind === 'pdf-to-jpg') {
        formData.append('resolution', settings.resolution);
        formData.append('quality', settings.quality);
    }

    if (props.toolKind === 'compress-pdf') {
        formData.append('compression', settings.compression);
    }

    if (props.toolKind === 'rotate-pdf') {
        formData.append('angle', settings.angle);
    }

    if (props.toolKind === 'protect-pdf' || props.toolKind === 'unlock-pdf') {
        formData.append('password', settings.password);
    }

    try {
        const response = await fetch(props.actionUrl, {
            method: 'POST',
            headers: { Accept: 'application/json' },
            body: formData,
        });
        const data = await response.json();

        if (!response.ok) {
            const message = data.message || data.errors?.file?.[0] || data.errors?.files?.[0] || 'This file could not be processed.';
            throw new Error(message);
        }

        result.value = data.file;
        setStatus('Result ready.');
        recordToolEvent(props.toolKind, 'generated', { count: files.value.length, format: data.file?.extension || 'file' });
    } catch (error) {
        setStatus(error.message || 'This file could not be processed.');
    } finally {
        isProcessing.value = false;
    }
}

onMounted(loadMetadata);
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">Free server-side tool</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">{{ limits.title }}</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">{{ limits.description }}</p>
            </div>

            <div
                class="grid min-h-48 place-items-center rounded-lg border border-dashed border-[#9ab2a8] bg-white px-4 py-8 text-center shadow-sm transition hover:border-[#2f7c67]"
                @dragover.prevent
                @drop="handleDrop"
            >
                <div>
                    <p class="text-base font-semibold text-[#171411]">{{ dropLabel }}</p>
                    <p class="mt-2 text-sm text-[#655d51]">Up to {{ limits.max_files }} file{{ limits.max_files === 1 ? '' : 's' }}, {{ maxUploadMb }} MB each.</p>
                    <label class="mt-5 inline-flex cursor-pointer rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus-within:ring-4 focus-within:ring-[#101820]/20">
                        {{ uploadLabel }}
                        <input type="file" :accept="limits.accept" :multiple="allowsMultiple" class="sr-only" @change="addFiles($event.target.files); $event.target.value = ''">
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
                        <button v-if="allowsMultiple" type="button" class="rounded-lg border border-[#c6d4cd] px-3 py-2 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] disabled:opacity-40" :disabled="index === 0" @click="moveFile(index, -1)">
                            Up
                        </button>
                        <button v-if="allowsMultiple" type="button" class="rounded-lg border border-[#c6d4cd] px-3 py-2 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] disabled:opacity-40" :disabled="index === files.length - 1" @click="moveFile(index, 1)">
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
                <div v-if="toolKind === 'remove-pdf-pages'" class="mb-4">
                    <label class="grid gap-2" for="pdf-pages">
                        <span class="text-sm font-medium text-[#2d2923]">Pages to remove</span>
                        <input id="pdf-pages" v-model="settings.pages" type="text" placeholder="1, 3-5" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                    </label>
                </div>

                <div v-if="toolKind === 'pdf-to-jpg'" class="mb-4 grid gap-4">
                    <label class="grid gap-2" for="pdf-resolution">
                        <span class="text-sm font-medium text-[#2d2923]">Resolution: {{ settings.resolution }} DPI</span>
                        <input id="pdf-resolution" v-model="settings.resolution" type="range" min="72" max="300" step="1" class="accent-[#2f7c67]">
                    </label>
                    <label class="grid gap-2" for="jpg-quality">
                        <span class="text-sm font-medium text-[#2d2923]">JPG quality: {{ settings.quality }}%</span>
                        <input id="jpg-quality" v-model="settings.quality" type="range" min="40" max="100" step="1" class="accent-[#2f7c67]">
                    </label>
                </div>

                <div v-if="toolKind === 'compress-pdf'" class="mb-4">
                    <label class="grid gap-2" for="pdf-compression">
                        <span class="text-sm font-medium text-[#2d2923]">Compression level</span>
                        <select id="pdf-compression" v-model="settings.compression" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                            <option value="screen">Smallest</option>
                            <option value="ebook">Balanced</option>
                            <option value="printer">Print</option>
                            <option value="prepress">High quality</option>
                        </select>
                    </label>
                </div>

                <div v-if="toolKind === 'rotate-pdf'" class="mb-4">
                    <label class="grid gap-2" for="pdf-angle">
                        <span class="text-sm font-medium text-[#2d2923]">Rotation</span>
                        <select id="pdf-angle" v-model="settings.angle" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                            <option :value="90">90 degrees</option>
                            <option :value="180">180 degrees</option>
                            <option :value="270">270 degrees</option>
                        </select>
                    </label>
                </div>

                <div v-if="toolKind === 'protect-pdf' || toolKind === 'unlock-pdf'" class="mb-4">
                    <label class="grid gap-2" for="pdf-password">
                        <span class="text-sm font-medium text-[#2d2923]">Password</span>
                        <input id="pdf-password" v-model="settings.password" type="password" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                    </label>
                </div>

                <button
                    type="button"
                    class="w-full rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus:outline-none focus:ring-4 focus:ring-[#101820]/20 disabled:cursor-not-allowed disabled:opacity-45"
                    :disabled="!canProcess"
                    @click="processFiles"
                >
                    {{ isProcessing ? 'Processing...' : actionLabel }}
                </button>

                <div v-if="result" class="mt-4 rounded-lg border border-[#cdd8d2] bg-[#f8faf8] p-4">
                    <p class="text-sm font-semibold text-[#171411]">File ready</p>
                    <p class="mt-1 text-sm text-[#655d51]">{{ formatSize(result.size) }} . deletes automatically in about 1 hour</p>
                    <a :href="result.download_url" class="mt-4 inline-flex w-full justify-center rounded-lg border border-[#2f7c67] bg-[#2f7c67] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#286b59]">
                        Download
                    </a>
                </div>

                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
