<script setup>
import { computed, reactive, ref } from 'vue';
import { recordToolEvent } from '../analytics';

const props = defineProps({
    toolKind: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
});

const file = ref(null);
const previewUrl = ref('');
const outputUrl = ref('');
const status = ref('');
const settings = reactive({
    text: 'ToolKitly',
    fontSize: 48,
    opacity: 70,
    color: '#ffffff',
    blur: 8,
    format: 'png',
    quality: 90,
});

const isWatermark = computed(() => props.toolKind === 'watermark-image');
const outputName = computed(() => {
    const name = file.value?.name?.replace(/\.[^.]+$/, '') || 'image';

    return `${name}-${isWatermark.value ? 'watermarked' : 'blurred'}-toolkitly.${settings.format === 'jpeg' ? 'jpg' : settings.format}`;
});

function setStatus(message) {
    status.value = message;
}

function addFile(selectedFile) {
    if (!selectedFile || !selectedFile.type.startsWith('image/')) {
        setStatus('Choose an image file.');
        return;
    }

    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
    }

    if (outputUrl.value) {
        URL.revokeObjectURL(outputUrl.value);
    }

    file.value = selectedFile;
    previewUrl.value = URL.createObjectURL(selectedFile);
    outputUrl.value = '';
    setStatus('Image ready.');
}

function loadImage() {
    return new Promise((resolve, reject) => {
        const image = new Image();

        image.onload = () => resolve(image);
        image.onerror = () => reject(new Error('This image could not be read.'));
        image.src = previewUrl.value;
    });
}

async function processImage() {
    if (!file.value) {
        setStatus('Choose an image first.');
        return;
    }

    const image = await loadImage();
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    canvas.width = image.naturalWidth;
    canvas.height = image.naturalHeight;

    if (isWatermark.value) {
        context.drawImage(image, 0, 0);
        context.globalAlpha = settings.opacity / 100;
        context.fillStyle = settings.color;
        context.font = `${settings.fontSize}px sans-serif`;
        context.textAlign = 'right';
        context.textBaseline = 'bottom';
        context.fillText(settings.text, canvas.width - 32, canvas.height - 32);
    } else {
        context.filter = `blur(${settings.blur}px)`;
        context.drawImage(image, 0, 0);
        context.filter = 'none';
    }

    canvas.toBlob((blob) => {
        if (!blob) {
            setStatus('This image could not be processed.');
            return;
        }

        if (outputUrl.value) {
            URL.revokeObjectURL(outputUrl.value);
        }

        outputUrl.value = URL.createObjectURL(blob);
        setStatus('Image ready to download.');
        recordToolEvent(props.toolKind, 'generated', { format: settings.format, mode: props.toolKind });
    }, `image/${settings.format}`, settings.quality / 100);
}

function downloadImage() {
    if (!outputUrl.value) {
        return;
    }

    const link = document.createElement('a');
    link.href = outputUrl.value;
    link.download = outputName.value;
    link.click();
}
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">Image tool</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">{{ title }}</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">{{ description }}</p>
            </div>

            <div class="grid gap-5">
                <div class="grid min-h-48 place-items-center rounded-lg border border-dashed border-[#9ab2a8] bg-white px-4 py-8 text-center shadow-sm">
                    <div>
                        <p class="text-base font-semibold text-[#171411]">Choose an image</p>
                        <p class="mt-2 text-sm text-[#655d51]">Processing happens in your browser.</p>
                        <label class="mt-5 inline-flex cursor-pointer rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b]">
                            Choose image
                            <input type="file" accept="image/*" class="sr-only" @change="addFile($event.target.files[0]); $event.target.value = ''">
                        </label>
                    </div>
                </div>

                <img v-if="outputUrl || previewUrl" :src="outputUrl || previewUrl" alt="" class="max-h-[520px] w-full rounded-lg border border-[#cdd8d2] bg-white object-contain p-2 shadow-sm">
            </div>
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                <div v-if="isWatermark" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Watermark text<input v-model="settings.text" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Font size {{ settings.fontSize }}px<input v-model="settings.fontSize" type="range" min="16" max="160" class="accent-[#2f7c67]"></label>
                    <label class="grid gap-2 text-sm font-medium">Opacity {{ settings.opacity }}%<input v-model="settings.opacity" type="range" min="10" max="100" class="accent-[#2f7c67]"></label>
                    <label class="grid gap-2 text-sm font-medium">Color<input v-model="settings.color" type="color" class="h-11"></label>
                </div>

                <div v-else class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Blur {{ settings.blur }}px<input v-model="settings.blur" type="range" min="1" max="40" class="accent-[#2f7c67]"></label>
                </div>

                <label class="mt-4 grid gap-2 text-sm font-medium">Format
                    <select v-model="settings.format" class="h-11 rounded-lg border border-[#cdd8d2] px-3">
                        <option value="png">PNG</option>
                        <option value="jpeg">JPG</option>
                        <option value="webp">WebP</option>
                    </select>
                </label>

                <button type="button" class="mt-5 w-full rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] disabled:opacity-45" :disabled="!file" @click="processImage">
                    {{ isWatermark ? 'Add watermark' : 'Blur image' }}
                </button>

                <button type="button" class="mt-3 w-full rounded-lg border border-[#2f7c67] px-4 py-3 text-sm font-semibold text-[#2f7c67] disabled:opacity-45" :disabled="!outputUrl" @click="downloadImage">
                    Download
                </button>

                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
