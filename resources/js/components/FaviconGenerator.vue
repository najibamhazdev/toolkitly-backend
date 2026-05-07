<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    metadataUrl: {
        type: String,
        required: true,
    },
});

const sourceFile = ref(null);
const sourceUrl = ref('');
const status = ref('');
const isGenerating = ref(false);
const icons = ref([]);
const icoUrl = ref('');
const limits = ref({
    max_upload_kb: 10240,
    sizes: [
        { size: 16, filename: 'favicon-16x16.png' },
        { size: 32, filename: 'favicon-32x32.png' },
        { size: 180, filename: 'apple-touch-icon.png' },
        { size: 192, filename: 'android-chrome-192x192.png' },
        { size: 512, filename: 'android-chrome-512x512.png' },
    ],
    ico_sizes: [16, 32, 48],
});

const maxUploadMb = computed(() => Math.round((limits.value.max_upload_kb / 1024) * 10) / 10);

function setStatus(message) {
    status.value = message;
}

function revokeOutputs() {
    icons.value.forEach((icon) => URL.revokeObjectURL(icon.url));
    icons.value = [];

    if (icoUrl.value) {
        URL.revokeObjectURL(icoUrl.value);
        icoUrl.value = '';
    }
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

function canvasBlob(size, image) {
    return new Promise((resolve, reject) => {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        const sourceSize = Math.min(image.naturalWidth, image.naturalHeight);
        const sourceX = Math.floor((image.naturalWidth - sourceSize) / 2);
        const sourceY = Math.floor((image.naturalHeight - sourceSize) / 2);

        canvas.width = size;
        canvas.height = size;
        context.imageSmoothingEnabled = true;
        context.imageSmoothingQuality = 'high';
        context.clearRect(0, 0, size, size);
        context.drawImage(image, sourceX, sourceY, sourceSize, sourceSize, 0, 0, size, size);

        canvas.toBlob((blob) => {
            if (blob) {
                resolve(blob);
                return;
            }

            reject(new Error('This icon could not be generated.'));
        }, 'image/png');
    });
}

async function pngEntry(size, filename, image) {
    const blob = await canvasBlob(size, image);

    return {
        size,
        filename,
        blob,
        url: URL.createObjectURL(blob),
    };
}

async function icoBlob(entries) {
    const buffers = await Promise.all(entries.map((entry) => entry.blob.arrayBuffer()));
    const headerSize = 6;
    const directorySize = 16 * buffers.length;
    let imageOffset = headerSize + directorySize;
    const totalSize = imageOffset + buffers.reduce((sum, buffer) => sum + buffer.byteLength, 0);
    const output = new ArrayBuffer(totalSize);
    const view = new DataView(output);
    const bytes = new Uint8Array(output);

    view.setUint16(0, 0, true);
    view.setUint16(2, 1, true);
    view.setUint16(4, buffers.length, true);

    buffers.forEach((buffer, index) => {
        const entry = entries[index];
        const directoryOffset = headerSize + index * 16;

        view.setUint8(directoryOffset, entry.size >= 256 ? 0 : entry.size);
        view.setUint8(directoryOffset + 1, entry.size >= 256 ? 0 : entry.size);
        view.setUint8(directoryOffset + 2, 0);
        view.setUint8(directoryOffset + 3, 0);
        view.setUint16(directoryOffset + 4, 1, true);
        view.setUint16(directoryOffset + 6, 32, true);
        view.setUint32(directoryOffset + 8, buffer.byteLength, true);
        view.setUint32(directoryOffset + 12, imageOffset, true);

        bytes.set(new Uint8Array(buffer), imageOffset);
        imageOffset += buffer.byteLength;
    });

    return new Blob([output], { type: 'image/x-icon' });
}

function addFile(file) {
    if (!file) {
        return;
    }

    if (!file.type.startsWith('image/')) {
        setStatus('Choose an image file.');
        return;
    }

    if (file.size > limits.value.max_upload_kb * 1024) {
        setStatus(`Choose an image up to ${maxUploadMb.value} MB.`);
        return;
    }

    if (sourceUrl.value) {
        URL.revokeObjectURL(sourceUrl.value);
    }

    revokeOutputs();
    sourceFile.value = file;
    sourceUrl.value = URL.createObjectURL(file);
    setStatus('Image ready.');
}

function handleDrop(event) {
    event.preventDefault();
    addFile(event.dataTransfer.files?.[0]);
}

async function generateFavicons() {
    if (!sourceFile.value) {
        setStatus('Choose an image before generating.');
        return;
    }

    isGenerating.value = true;
    revokeOutputs();
    setStatus('Generating favicons...');

    try {
        const image = await loadImage(sourceFile.value);
        const pngs = [];

        for (const item of limits.value.sizes) {
            pngs.push(await pngEntry(item.size, item.filename, image));
        }

        const icoEntries = [];

        for (const size of limits.value.ico_sizes) {
            icoEntries.push(await pngEntry(size, `favicon-${size}x${size}.png`, image));
        }

        icons.value = pngs;
        icoUrl.value = URL.createObjectURL(await icoBlob(icoEntries));
        icoEntries.forEach((entry) => URL.revokeObjectURL(entry.url));
        setStatus('Favicons ready.');
    } catch (error) {
        setStatus(error.message || 'Favicons could not be generated.');
    } finally {
        isGenerating.value = false;
    }
}

function download(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.click();
}

function downloadAll() {
    if (icoUrl.value) {
        download(icoUrl.value, 'favicon.ico');
    }

    icons.value.forEach((icon) => download(icon.url, icon.filename));
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
        setStatus('Using local favicon settings.');
    }
}

onMounted(loadMetadata);
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">Free browser-based tool</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">Favicon Generator</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">
                    Generate favicon.ico, browser icons, Apple touch icons, and Android app icons from one source image.
                </p>
            </div>

            <div
                class="grid min-h-48 place-items-center rounded-lg border border-dashed border-[#9ab2a8] bg-white px-4 py-8 text-center shadow-sm transition hover:border-[#2f7c67]"
                @dragover.prevent
                @drop="handleDrop"
            >
                <div>
                    <p class="text-base font-semibold text-[#171411]">Drop one image here</p>
                    <p class="mt-2 text-sm text-[#655d51]">Use a square PNG or high-resolution logo when possible. Up to {{ maxUploadMb }} MB.</p>
                    <label class="mt-5 inline-flex cursor-pointer rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus-within:ring-4 focus-within:ring-[#101820]/20">
                        Choose image
                        <input type="file" accept="image/*" class="sr-only" @change="addFile($event.target.files?.[0]); $event.target.value = ''">
                    </label>
                </div>
            </div>

            <div v-if="icons.length" class="mt-5 grid gap-3 sm:grid-cols-2">
                <div v-for="icon in icons" :key="icon.filename" class="grid grid-cols-[56px_minmax(0,1fr)_auto] items-center gap-3 rounded-lg border border-[#cdd8d2] bg-white p-3 shadow-sm">
                    <div class="grid size-14 place-items-center rounded-lg bg-[#eef4f1]">
                        <img :src="icon.url" alt="" class="max-h-10 max-w-10">
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-[#171411]">{{ icon.filename }}</p>
                        <p class="text-sm text-[#655d51]">{{ icon.size }}×{{ icon.size }}</p>
                    </div>
                    <button type="button" class="rounded-lg border border-[#c6d4cd] px-3 py-2 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80]" @click="download(icon.url, icon.filename)">
                        Download
                    </button>
                </div>
            </div>
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                <div class="grid aspect-square place-items-center overflow-hidden rounded-lg border border-[#dfe7e2] bg-[#f8faf8] p-4">
                    <img v-if="sourceUrl" :src="sourceUrl" alt="" class="max-h-full max-w-full object-contain">
                    <p v-else class="text-sm text-[#655d51]">Preview appears here</p>
                </div>

                <button
                    type="button"
                    class="mt-4 w-full rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus:outline-none focus:ring-4 focus:ring-[#101820]/20 disabled:cursor-not-allowed disabled:opacity-45"
                    :disabled="!sourceFile || isGenerating"
                    @click="generateFavicons"
                >
                    {{ isGenerating ? 'Generating...' : 'Generate favicons' }}
                </button>

                <button
                    type="button"
                    class="mt-3 w-full rounded-lg border border-[#c6d4cd] px-4 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] hover:bg-[#f4f7f5] focus:outline-none focus:ring-4 focus:ring-[#101820]/10 disabled:cursor-not-allowed disabled:opacity-45"
                    :disabled="!icons.length || !icoUrl"
                    @click="downloadAll"
                >
                    Download all
                </button>

                <button
                    type="button"
                    class="mt-3 w-full rounded-lg border border-[#c6d4cd] px-4 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] hover:bg-[#f4f7f5] focus:outline-none focus:ring-4 focus:ring-[#101820]/10 disabled:cursor-not-allowed disabled:opacity-45"
                    :disabled="!icoUrl"
                    @click="download(icoUrl, 'favicon.ico')"
                >
                    Download favicon.ico
                </button>

                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
