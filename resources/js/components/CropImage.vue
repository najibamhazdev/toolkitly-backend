<script setup>
import { computed, reactive, ref } from 'vue';
import { recordToolEvent } from '../analytics';

const file = ref(null);
const previewUrl = ref('');
const outputUrl = ref('');
const status = ref('');
const imageSize = reactive({ width: 0, height: 0 });
const crop = reactive({ x: 0, y: 0, width: 800, height: 800, format: 'png', quality: 90 });

const outputName = computed(() => {
    const name = file.value?.name?.replace(/\.[^.]+$/, '') || 'image';

    return `${name}-cropped-toolkitly.${crop.format === 'jpeg' ? 'jpg' : crop.format}`;
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

    const image = new Image();
    image.onload = () => {
        imageSize.width = image.naturalWidth;
        imageSize.height = image.naturalHeight;
        crop.x = 0;
        crop.y = 0;
        crop.width = Math.min(image.naturalWidth, 800);
        crop.height = Math.min(image.naturalHeight, 800);
        setStatus('Image ready.');
    };
    image.src = previewUrl.value;
}

function loadImage() {
    return new Promise((resolve, reject) => {
        const image = new Image();

        image.onload = () => resolve(image);
        image.onerror = () => reject(new Error('This image could not be read.'));
        image.src = previewUrl.value;
    });
}

async function cropImage() {
    if (!file.value) {
        setStatus('Choose an image before cropping.');
        return;
    }

    const x = Math.max(0, Math.min(Number(crop.x) || 0, imageSize.width - 1));
    const y = Math.max(0, Math.min(Number(crop.y) || 0, imageSize.height - 1));
    const width = Math.max(1, Math.min(Number(crop.width) || 1, imageSize.width - x));
    const height = Math.max(1, Math.min(Number(crop.height) || 1, imageSize.height - y));
    const image = await loadImage();
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    canvas.width = width;
    canvas.height = height;
    context.drawImage(image, x, y, width, height, 0, 0, width, height);

    canvas.toBlob((blob) => {
        if (!blob) {
            setStatus('This image could not be cropped.');
            return;
        }

        if (outputUrl.value) {
            URL.revokeObjectURL(outputUrl.value);
        }

        outputUrl.value = URL.createObjectURL(blob);
        setStatus('Cropped image ready.');
        recordToolEvent('crop-image', 'generated', { format: crop.format });
    }, `image/${crop.format}`, crop.quality / 100);
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
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">Crop Image</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">Crop JPG, PNG, and WebP images locally in your browser.</p>
            </div>

            <div class="grid gap-5">
                <div class="grid min-h-48 place-items-center rounded-lg border border-dashed border-[#9ab2a8] bg-white px-4 py-8 text-center shadow-sm">
                    <div>
                        <p class="text-base font-semibold text-[#171411]">Choose an image</p>
                        <p class="mt-2 text-sm text-[#655d51]">The crop happens in your browser.</p>
                        <label class="mt-5 inline-flex cursor-pointer rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b]">
                            Choose image
                            <input type="file" accept="image/*" class="sr-only" @change="addFile($event.target.files[0]); $event.target.value = ''">
                        </label>
                    </div>
                </div>

                <img v-if="previewUrl" :src="previewUrl" alt="" class="max-h-[520px] w-full rounded-lg border border-[#cdd8d2] bg-white object-contain p-2 shadow-sm">
            </div>
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                <div class="grid sm:grid-cols-2 gap-3">
                    <label class="grid gap-2 text-sm font-medium">X<input v-model="crop.x" type="number" min="0" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Y<input v-model="crop.y" type="number" min="0" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Width<input v-model="crop.width" type="number" min="1" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Height<input v-model="crop.height" type="number" min="1" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <label class="mt-4 grid gap-2 text-sm font-medium">Format
                    <select v-model="crop.format" class="h-11 rounded-lg border border-[#cdd8d2] px-3">
                        <option value="png">PNG</option>
                        <option value="jpeg">JPG</option>
                        <option value="webp">WebP</option>
                    </select>
                </label>

                <label class="mt-4 grid gap-2 text-sm font-medium">Quality {{ crop.quality }}%
                    <input v-model="crop.quality" type="range" min="40" max="100" class="accent-[#2f7c67]">
                </label>

                <button type="button" class="mt-5 w-full rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] disabled:opacity-45" :disabled="!file" @click="cropImage">
                    Crop image
                </button>

                <button type="button" class="mt-3 w-full rounded-lg border border-[#2f7c67] px-4 py-3 text-sm font-semibold text-[#2f7c67] disabled:opacity-45" :disabled="!outputUrl" @click="downloadImage">
                    Download
                </button>

                <p v-if="imageSize.width" class="mt-3 text-sm text-[#655d51]">{{ imageSize.width }}x{{ imageSize.height }} source image</p>
                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
