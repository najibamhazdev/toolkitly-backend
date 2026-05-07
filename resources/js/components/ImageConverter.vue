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
const isConverting = ref(false);
const limits = ref({
    max_files: 20,
    max_upload_kb: 10240,
    formats: [
        { value: 'jpeg', label: 'JPG', mime: 'image/jpeg' },
        { value: 'png', label: 'PNG', mime: 'image/png' },
        { value: 'webp', label: 'WebP', mime: 'image/webp' },
    ],
    defaults: {
        format: 'webp',
        quality: 90,
    },
    examples: [
        { from: 'JPG', to: 'PNG' },
        { from: 'PNG', to: 'WebP' },
        { from: 'WebP', to: 'JPG' },
    ],
});
const settings = reactive({
    format: 'webp',
    quality: 90,
});

const maxUploadMb = computed(() => Math.round((limits.value.max_upload_kb / 1024) * 10) / 10);
const selectedFormat = computed(() => limits.value.formats.find((format) => format.value === settings.format) || limits.value.formats[0]);

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
            outputSize: 0,
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
        setStatus(`Only ${limits.value.max_files} images can be converted at once.`);
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

            reject(new Error('This image could not be converted.'));
        }, selectedFormat.value.mime, settings.quality / 100);
    });
}

async function convertEntry(entry) {
    if (entry.outputUrl) {
        URL.revokeObjectURL(entry.outputUrl);
        entry.outputUrl = '';
    }

    const image = await loadImage(entry.file);
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    canvas.width = image.naturalWidth;
    canvas.height = image.naturalHeight;
    context.fillStyle = '#ffffff';
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.drawImage(image, 0, 0);

    const blob = await canvasToBlob(canvas);

    entry.width = image.naturalWidth;
    entry.height = image.naturalHeight;
    entry.outputUrl = URL.createObjectURL(blob);
    entry.outputSize = blob.size;
    entry.error = '';
}

async function convertImages() {
    if (!files.value.length) {
        setStatus('Add images before converting.');
        return;
    }

    isConverting.value = true;
    setStatus('Converting images...');

    for (const entry of files.value) {
        try {
            await convertEntry(entry);
        } catch (error) {
            entry.error = error.message || 'This image could not be converted.';
        }
    }

    isConverting.value = false;
    setStatus('Converted images ready.');
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
        settings.format = data.defaults?.format || settings.format;
        settings.quality = data.defaults?.quality || settings.quality;
    } catch {
        setStatus('Using local image conversion settings.');
    }
}

onMounted(loadMetadata);
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">Free browser-based tool</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">Image Converter</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">
                    Convert JPG, PNG, and WebP images in your browser. Your files stay on this device.
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
                            <span v-if="entry.outputSize"> -> {{ formatSize(entry.outputSize) }}</span>
                        </p>
                        <p v-if="entry.width" class="mt-1 text-sm text-[#655d51]">{{ entry.width }}x{{ entry.height }} . {{ selectedFormat.label }}</p>
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
                        <span class="text-sm font-medium text-[#2d2923]">Convert to</span>
                        <select id="image-format" v-model="settings.format" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                            <option v-for="format in limits.formats" :key="format.value" :value="format.value">{{ format.label }}</option>
                        </select>
                    </label>

                    <label class="grid gap-2" for="image-quality">
                        <span class="text-sm font-medium text-[#2d2923]">Quality: {{ settings.quality }}%</span>
                        <input id="image-quality" v-model="settings.quality" type="range" min="35" max="100" step="1" class="accent-[#2f7c67]">
                    </label>
                </div>

                <div class="mt-5 rounded-lg border border-[#cdd8d2] bg-[#f8faf8] p-4">
                    <p class="text-sm font-semibold text-[#171411]">Popular conversions</p>
                    <div class="mt-3 grid grid-cols-3 gap-2">
                        <button
                            v-for="example in limits.examples"
                            :key="`${example.from}-${example.to}`"
                            type="button"
                            class="rounded-lg border border-[#c6d4cd] px-2 py-2 text-xs font-semibold text-[#47534d] transition hover:border-[#2f7c67] hover:text-[#171411]"
                            @click="settings.format = example.to === 'JPG' ? 'jpeg' : example.to.toLowerCase()"
                        >
                            {{ example.from }} to {{ example.to }}
                        </button>
                    </div>
                </div>

                <button
                    type="button"
                    class="mt-5 w-full rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus:outline-none focus:ring-4 focus:ring-[#101820]/20 disabled:cursor-not-allowed disabled:opacity-45"
                    :disabled="!files.length || isConverting"
                    @click="convertImages"
                >
                    {{ isConverting ? 'Converting...' : 'Convert images' }}
                </button>

                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
