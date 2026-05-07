<script setup>
import { computed, onMounted, reactive, ref } from 'vue';

const props = defineProps({
    metadataUrl: {
        type: String,
        required: true,
    },
});

const sourceFile = ref(null);
const sourceUrl = ref('');
const outputUrl = ref('');
const outputSize = ref(0);
const sourceDimensions = ref('');
const status = ref('');
const isResizing = ref(false);
const limits = ref({
    max_upload_kb: 10240,
    formats: [
        { value: 'jpeg', label: 'JPG', mime: 'image/jpeg' },
        { value: 'png', label: 'PNG', mime: 'image/png' },
        { value: 'webp', label: 'WebP', mime: 'image/webp' },
    ],
    defaults: {
        format: 'jpeg',
        quality: 88,
        fit: 'cover',
        background: '#ffffff',
    },
    presets: [],
});
const settings = reactive({
    width: 1080,
    height: 1080,
    format: 'jpeg',
    quality: 88,
    fit: 'cover',
    background: '#ffffff',
    preset: 'Instagram Square Post',
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

function outputName() {
    const name = sourceFile.value?.name?.replace(/\.[^.]+$/, '') || 'image';
    const size = `${settings.width}x${settings.height}`;

    return `${name}-${size}-toolkitly.${extension()}`;
}

function revokeOutput() {
    if (outputUrl.value) {
        URL.revokeObjectURL(outputUrl.value);
        outputUrl.value = '';
        outputSize.value = 0;
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

function canvasToBlob(canvas) {
    return new Promise((resolve, reject) => {
        canvas.toBlob((blob) => {
            if (blob) {
                resolve(blob);
                return;
            }

            reject(new Error('This image could not be resized.'));
        }, selectedFormat.value.mime, settings.quality / 100);
    });
}

function applyPreset(item) {
    settings.preset = item.label;
    settings.width = item.width;
    settings.height = item.height;
    revokeOutput();
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

    revokeOutput();
    sourceFile.value = file;
    sourceUrl.value = URL.createObjectURL(file);
    sourceDimensions.value = '';
    setStatus('Image ready.');
}

function handleDrop(event) {
    event.preventDefault();
    addFile(event.dataTransfer.files?.[0]);
}

async function resizeImage() {
    if (!sourceFile.value) {
        setStatus('Choose an image before resizing.');
        return;
    }

    const targetWidth = Number(settings.width);
    const targetHeight = Number(settings.height);

    if (targetWidth < 1 || targetHeight < 1) {
        setStatus('Enter valid output dimensions.');
        return;
    }

    isResizing.value = true;
    revokeOutput();
    setStatus('Resizing image...');

    try {
        const image = await loadImage(sourceFile.value);
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        const sourceRatio = image.naturalWidth / image.naturalHeight;
        const targetRatio = targetWidth / targetHeight;
        let drawWidth = targetWidth;
        let drawHeight = targetHeight;
        let drawX = 0;
        let drawY = 0;

        canvas.width = targetWidth;
        canvas.height = targetHeight;
        context.fillStyle = settings.background;
        context.fillRect(0, 0, targetWidth, targetHeight);
        context.imageSmoothingEnabled = true;
        context.imageSmoothingQuality = 'high';

        if (settings.fit === 'cover') {
            if (sourceRatio > targetRatio) {
                drawHeight = targetHeight;
                drawWidth = targetHeight * sourceRatio;
                drawX = (targetWidth - drawWidth) / 2;
            } else {
                drawWidth = targetWidth;
                drawHeight = targetWidth / sourceRatio;
                drawY = (targetHeight - drawHeight) / 2;
            }
        } else {
            if (sourceRatio > targetRatio) {
                drawWidth = targetWidth;
                drawHeight = targetWidth / sourceRatio;
                drawY = (targetHeight - drawHeight) / 2;
            } else {
                drawHeight = targetHeight;
                drawWidth = targetHeight * sourceRatio;
                drawX = (targetWidth - drawWidth) / 2;
            }
        }

        context.drawImage(image, drawX, drawY, drawWidth, drawHeight);

        const blob = await canvasToBlob(canvas);
        outputUrl.value = URL.createObjectURL(blob);
        outputSize.value = blob.size;
        sourceDimensions.value = `${image.naturalWidth}x${image.naturalHeight}`;
        setStatus('Resized image ready.');
    } catch (error) {
        setStatus(error.message || 'This image could not be resized.');
    } finally {
        isResizing.value = false;
    }
}

function downloadImage() {
    if (!outputUrl.value) {
        return;
    }

    const link = document.createElement('a');
    link.href = outputUrl.value;
    link.download = outputName();
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
        settings.fit = data.defaults?.fit || settings.fit;
        settings.background = data.defaults?.background || settings.background;

        const firstPreset = data.presets?.[0]?.items?.[0];

        if (firstPreset) {
            applyPreset(firstPreset);
        }
    } catch {
        setStatus('Using local image resize settings.');
    }
}

onMounted(loadMetadata);
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_430px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">Free browser-based tool</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">Image Resizer</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">
                    Resize images for Instagram, Facebook, LinkedIn, or custom dimensions. Your images stay on this device.
                </p>
            </div>

            <div
                class="grid min-h-48 place-items-center rounded-lg border border-dashed border-[#9ab2a8] bg-white px-4 py-8 text-center shadow-sm transition hover:border-[#2f7c67]"
                @dragover.prevent
                @drop="handleDrop"
            >
                <div>
                    <p class="text-base font-semibold text-[#171411]">Drop one image here</p>
                    <p class="mt-2 text-sm text-[#655d51]">JPG, PNG, WebP, and other browser-supported images. Up to {{ maxUploadMb }} MB.</p>
                    <label class="mt-5 inline-flex cursor-pointer rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus-within:ring-4 focus-within:ring-[#101820]/20">
                        Choose image
                        <input type="file" accept="image/*" class="sr-only" @change="addFile($event.target.files?.[0]); $event.target.value = ''">
                    </label>
                </div>
            </div>

            <div class="mt-6 grid gap-5">
                <section v-for="group in limits.presets" :key="group.group">
                    <h2 class="mb-2 text-sm font-semibold text-[#2d2923]">{{ group.group }}</h2>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="item in group.items"
                            :key="item.label"
                            type="button"
                            class="rounded-lg border px-3 py-2 text-sm font-semibold transition focus:outline-none focus:ring-4 focus:ring-[#2f7c67]/15"
                            :class="settings.preset === item.label ? 'border-[#2f7c67] bg-[#2f7c67] text-white' : 'border-[#cdd8d2] bg-white text-[#47534d] hover:border-[#6d8d80]'"
                            @click="applyPreset(item)"
                        >
                            {{ item.label }} · {{ item.width }}×{{ item.height }}
                        </button>
                    </div>
                </section>
            </div>
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                <div class="grid aspect-square place-items-center overflow-hidden rounded-lg border border-[#dfe7e2] bg-[#f8faf8] p-4">
                    <img v-if="outputUrl" :src="outputUrl" alt="" class="max-h-full max-w-full object-contain">
                    <img v-else-if="sourceUrl" :src="sourceUrl" alt="" class="max-h-full max-w-full object-contain">
                    <p v-else class="text-sm text-[#655d51]">Preview appears here</p>
                </div>

                <div class="mt-4 grid gap-4">
                    <div class="grid grid-cols-2 gap-3">
                        <label class="grid gap-2" for="image-width">
                            <span class="text-sm font-medium text-[#2d2923]">Width</span>
                            <input id="image-width" v-model="settings.width" type="number" min="1" max="8000" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15" @input="settings.preset = 'Custom'; revokeOutput()">
                        </label>
                        <label class="grid gap-2" for="image-height">
                            <span class="text-sm font-medium text-[#2d2923]">Height</span>
                            <input id="image-height" v-model="settings.height" type="number" min="1" max="8000" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15" @input="settings.preset = 'Custom'; revokeOutput()">
                        </label>
                    </div>

                    <label class="grid gap-2" for="image-fit">
                        <span class="text-sm font-medium text-[#2d2923]">Fit</span>
                        <select id="image-fit" v-model="settings.fit" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15" @change="revokeOutput">
                            <option value="cover">Cover / crop to fill</option>
                            <option value="contain">Contain / add padding</option>
                        </select>
                    </label>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="grid gap-2" for="image-format">
                            <span class="text-sm font-medium text-[#2d2923]">Format</span>
                            <select id="image-format" v-model="settings.format" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15" @change="revokeOutput">
                                <option v-for="format in limits.formats" :key="format.value" :value="format.value">{{ format.label }}</option>
                            </select>
                        </label>
                        <label class="grid gap-2" for="image-background">
                            <span class="text-sm font-medium text-[#2d2923]">Background</span>
                            <input id="image-background" v-model="settings.background" type="color" class="h-11 w-full rounded-lg border border-[#cdd8d2] bg-white p-1" @input="revokeOutput">
                        </label>
                    </div>

                    <label class="grid gap-2" for="image-quality">
                        <span class="text-sm font-medium text-[#2d2923]">Quality: {{ settings.quality }}%</span>
                        <input id="image-quality" v-model="settings.quality" type="range" min="35" max="95" step="1" class="accent-[#2f7c67]" @input="revokeOutput">
                    </label>
                </div>

                <button
                    type="button"
                    class="mt-5 w-full rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus:outline-none focus:ring-4 focus:ring-[#101820]/20 disabled:cursor-not-allowed disabled:opacity-45"
                    :disabled="!sourceFile || isResizing"
                    @click="resizeImage"
                >
                    {{ isResizing ? 'Resizing...' : 'Resize image' }}
                </button>

                <button
                    type="button"
                    class="mt-3 w-full rounded-lg border border-[#c6d4cd] px-4 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] hover:bg-[#f4f7f5] focus:outline-none focus:ring-4 focus:ring-[#101820]/10 disabled:cursor-not-allowed disabled:opacity-45"
                    :disabled="!outputUrl"
                    @click="downloadImage"
                >
                    Download image
                </button>

                <p v-if="outputUrl" class="mt-3 text-sm text-[#655d51]">
                    {{ sourceDimensions }} → {{ settings.width }}×{{ settings.height }} · {{ formatSize(outputSize) }}
                </p>
                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
