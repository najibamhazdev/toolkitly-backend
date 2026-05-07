<script setup>
import { computed, onMounted, reactive, ref } from 'vue';

const props = defineProps({
    metadataUrl: {
        type: String,
        required: true,
    },
});

const files = ref([]);
const status = ref('');
const limits = ref({
    max_files: 20,
    max_upload_kb: 10240,
    formats: [
        { value: 'jpeg', label: 'JPG', mime: 'image/jpeg' },
        { value: 'png', label: 'PNG', mime: 'image/png' },
        { value: 'webp', label: 'WebP', mime: 'image/webp' },
    ],
    defaults: {
        quality: 72,
        max_width: 1920,
        format: 'jpeg',
    },
});
const settings = reactive({
    quality: 72,
    maxWidth: 1920,
    format: 'jpeg',
});

const isProcessing = ref(false);

const maxUploadMb = computed(() => Math.round((limits.value.max_upload_kb / 1024) * 10) / 10);

const selectedFormat = computed(() => limits.value.formats.find((format) => format.value === settings.format) || limits.value.formats[0]);

const totalOriginal = computed(() => files.value.reduce((sum, entry) => sum + entry.file.size, 0));

const totalCompressed = computed(() => files.value.reduce((sum, entry) => sum + (entry.compressedSize || 0), 0));

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

function savings(entry) {
    if (!entry.compressedSize) {
        return '';
    }

    const percent = Math.round((1 - entry.compressedSize / entry.file.size) * 100);

    return percent > 0 ? `${percent}% smaller` : 'Optimized copy ready';
}

function extension() {
    return settings.format === 'jpeg' ? 'jpg' : settings.format;
}

function outputName(name) {
    const clean = name.replace(/\.[^.]+$/, '');

    return `${clean}-toolkitly.${extension()}`;
}

function addFiles(selectedFiles) {
    const incoming = Array.from(selectedFiles || []);
    const maxBytes = limits.value.max_upload_kb * 1024;
    const images = incoming.filter((file) => file.type.startsWith('image/') && file.size <= maxBytes);
    const availableSlots = limits.value.max_files - files.value.length;

    files.value = [
        ...files.value,
        ...images.slice(0, Math.max(0, availableSlots)).map((file) => ({
            id: crypto.randomUUID(),
            file,
            previewUrl: URL.createObjectURL(file),
            outputUrl: '',
            compressedSize: 0,
            width: 0,
            height: 0,
            error: '',
        })),
    ];

    if (images.length !== incoming.length) {
        setStatus(`Only images up to ${maxUploadMb.value} MB were added.`);
        return;
    }

    if (images.length > availableSlots) {
        setStatus(`Only ${limits.value.max_files} images can be processed at once.`);
        return;
    }

    setStatus(`${files.value.length} image${files.value.length === 1 ? '' : 's'} ready.`);
}

function handleDrop(event) {
    event.preventDefault();
    addFiles(event.dataTransfer.files);
}

function removeFile(id) {
    const entry = files.value.find((item) => item.id === id);

    if (entry) {
        URL.revokeObjectURL(entry.previewUrl);
        if (entry.outputUrl) {
            URL.revokeObjectURL(entry.outputUrl);
        }
    }

    files.value = files.value.filter((item) => item.id !== id);
    setStatus(`${files.value.length} image${files.value.length === 1 ? '' : 's'} ready.`);
}

function loadImage(file) {
    return new Promise((resolve, reject) => {
        const image = new Image();
        const url = URL.createObjectURL(file);

        image.onload = () => {
            URL.revokeObjectURL(url);
            resolve(image);
        };
        image.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error('This image could not be read.'));
        };
        image.src = url;
    });
}

function canvasToBlob(canvas) {
    return new Promise((resolve, reject) => {
        canvas.toBlob((blob) => {
            if (blob) {
                resolve(blob);
                return;
            }

            reject(new Error('This image could not be compressed.'));
        }, selectedFormat.value.mime, settings.quality / 100);
    });
}

async function compressEntry(entry) {
    if (entry.outputUrl) {
        URL.revokeObjectURL(entry.outputUrl);
        entry.outputUrl = '';
    }

    const image = await loadImage(entry.file);
    const ratio = settings.maxWidth > 0 && image.naturalWidth > settings.maxWidth
        ? settings.maxWidth / image.naturalWidth
        : 1;
    const width = Math.max(1, Math.round(image.naturalWidth * ratio));
    const height = Math.max(1, Math.round(image.naturalHeight * ratio));
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    canvas.width = width;
    canvas.height = height;
    context.drawImage(image, 0, 0, width, height);

    const blob = await canvasToBlob(canvas);

    entry.width = width;
    entry.height = height;
    entry.outputUrl = URL.createObjectURL(blob);
    entry.compressedSize = blob.size;
    entry.error = '';
}

async function compressImages() {
    if (!files.value.length) {
        setStatus('Add images before compressing.');
        return;
    }

    isProcessing.value = true;
    setStatus('Compressing images...');

    for (const entry of files.value) {
        try {
            await compressEntry(entry);
        } catch (error) {
            entry.error = error.message || 'This image could not be compressed.';
        }
    }

    isProcessing.value = false;
    setStatus('Compressed images ready.');
}

function downloadEntry(entry) {
    if (!entry.outputUrl) {
        return;
    }

    const link = document.createElement('a');
    link.href = entry.outputUrl;
    link.download = outputName(entry.file.name);
    link.click();
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
        settings.quality = data.defaults?.quality || settings.quality;
        settings.maxWidth = data.defaults?.max_width || settings.maxWidth;
        settings.format = data.defaults?.format || settings.format;
    } catch {
        setStatus('Using local image settings.');
    }
}

onMounted(loadMetadata);
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">Free browser-based tool</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">Image Compressor</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">
                    Compress, resize, and convert images in your browser. Your images stay on this device.
                </p>
            </div>

            <div
                class="grid min-h-48 place-items-center rounded-lg border border-dashed border-[#9ab2a8] bg-white px-4 py-8 text-center shadow-sm transition hover:border-[#2f7c67]"
                @dragover.prevent
                @drop="handleDrop"
            >
                <div>
                    <p class="text-base font-semibold text-[#171411]">Drop images here</p>
                    <p class="mt-2 text-sm text-[#655d51]">JPG, PNG, WebP, and other browser-supported images. Up to {{ limits.max_files }} files, {{ maxUploadMb }} MB each.</p>
                    <label class="mt-5 inline-flex cursor-pointer rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus-within:ring-4 focus-within:ring-[#101820]/20">
                        Choose images
                        <input type="file" accept="image/*" multiple class="sr-only" @change="addFiles($event.target.files); $event.target.value = ''">
                    </label>
                </div>
            </div>

            <div v-if="files.length" class="mt-5 grid gap-3">
                <div
                    v-for="entry in files"
                    :key="entry.id"
                    class="grid gap-3 rounded-lg border border-[#cdd8d2] bg-white p-3 shadow-sm sm:grid-cols-[72px_minmax(0,1fr)_auto]"
                >
                    <img :src="entry.previewUrl" alt="" class="size-[72px] rounded-lg object-cover">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-[#171411]">{{ entry.file.name }}</p>
                        <p class="mt-1 text-sm text-[#655d51]">
                            {{ formatSize(entry.file.size) }}
                            <span v-if="entry.compressedSize"> → {{ formatSize(entry.compressedSize) }}</span>
                        </p>
                        <p v-if="entry.width" class="mt-1 text-sm text-[#655d51]">{{ entry.width }}×{{ entry.height }} · {{ savings(entry) }}</p>
                        <p v-if="entry.error" class="mt-1 text-sm text-[#9b3636]">{{ entry.error }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="rounded-lg border border-[#c6d4cd] px-3 py-2 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] disabled:opacity-40" :disabled="!entry.outputUrl" @click="downloadEntry(entry)">
                            Download
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
                <div class="grid gap-4">
                    <label class="grid gap-2" for="image-format">
                        <span class="text-sm font-medium text-[#2d2923]">Output format</span>
                        <select id="image-format" v-model="settings.format" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                            <option v-for="format in limits.formats" :key="format.value" :value="format.value">{{ format.label }}</option>
                        </select>
                    </label>

                    <label class="grid gap-2" for="image-quality">
                        <span class="text-sm font-medium text-[#2d2923]">Quality: {{ settings.quality }}%</span>
                        <input id="image-quality" v-model="settings.quality" type="range" min="35" max="95" step="1" class="accent-[#2f7c67]">
                    </label>

                    <label class="grid gap-2" for="image-width">
                        <span class="text-sm font-medium text-[#2d2923]">Max width</span>
                        <input id="image-width" v-model="settings.maxWidth" type="number" min="320" max="8000" step="10" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                    </label>
                </div>

                <button
                    type="button"
                    class="mt-5 w-full rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus:outline-none focus:ring-4 focus:ring-[#101820]/20 disabled:cursor-not-allowed disabled:opacity-45"
                    :disabled="!files.length || isProcessing"
                    @click="compressImages"
                >
                    {{ isProcessing ? 'Compressing...' : 'Compress images' }}
                </button>

                <div v-if="totalCompressed" class="mt-4 rounded-lg border border-[#cdd8d2] bg-[#f8faf8] p-4">
                    <p class="text-sm font-semibold text-[#171411]">Batch result</p>
                    <p class="mt-1 text-sm text-[#655d51]">{{ formatSize(totalOriginal) }} → {{ formatSize(totalCompressed) }}</p>
                </div>

                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
