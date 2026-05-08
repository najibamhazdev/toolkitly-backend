<script setup>
import bwipjs from 'bwip-js';
import { computed, nextTick, onMounted, reactive, ref, watch } from 'vue';
import { recordToolEvent } from '../analytics';

const props = defineProps({
    metadataUrl: {
        type: String,
        required: true,
    },
    payloadUrl: {
        type: String,
        required: true,
    },
});

const canvas = ref(null);
const status = ref('');
const types = ref([
    {
        value: 'code128',
        label: 'Code 128',
        bcid: 'code128',
        placeholder: 'TK-2026-0001',
        help: 'Letters, numbers, spaces, and common symbols.',
    },
    {
        value: 'ean13',
        label: 'EAN-13',
        bcid: 'ean13',
        placeholder: '5901234123457',
        help: '12 or 13 digits for retail product codes.',
    },
    {
        value: 'upc',
        label: 'UPC-A',
        bcid: 'upca',
        placeholder: '042100005264',
        help: '11 or 12 digits for UPC-A product codes.',
    },
]);

const form = reactive({
    type: 'code128',
    text: 'TK-2026-0001',
    scale: 3,
    height: 12,
    include_text: true,
});

let renderHandle;

const selectedType = computed(() => types.value.find((type) => type.value === form.type) || types.value[0]);

const normalizedText = computed(() => {
    if (form.type === 'code128') {
        return form.text.trim();
    }

    return form.text.replace(/\D+/g, '');
});

const validationMessage = computed(() => {
    if (!normalizedText.value) {
        return 'Enter barcode content.';
    }

    if (form.type === 'ean13' && !/^\d{12,13}$/.test(normalizedText.value)) {
        return 'EAN-13 needs 12 or 13 digits.';
    }

    if (form.type === 'upc' && !/^\d{11,12}$/.test(normalizedText.value)) {
        return 'UPC-A needs 11 or 12 digits.';
    }

    return '';
});

const canRender = computed(() => validationMessage.value === '');

const renderOptions = computed(() => ({
    bcid: selectedType.value.bcid,
    text: normalizedText.value,
    scale: Number(form.scale),
    height: Number(form.height),
    includetext: form.include_text,
    textxalign: 'center',
    backgroundcolor: 'ffffff',
}));

function setStatus(message) {
    status.value = message;
}

function scheduleRender() {
    window.clearTimeout(renderHandle);
    renderHandle = window.setTimeout(render, 80);
}

async function render() {
    if (!canvas.value) {
        return;
    }

    if (!canRender.value) {
        const context = canvas.value.getContext('2d');
        context.clearRect(0, 0, canvas.value.width, canvas.value.height);
        setStatus(validationMessage.value);
        return;
    }

    try {
        bwipjs.toCanvas(canvas.value, renderOptions.value);
        setStatus(`${selectedType.value.label} barcode ready.`);
    } catch (error) {
        setStatus(error.message || 'This barcode could not be generated.');
    }
}

function download(href, extension) {
    const link = document.createElement('a');
    link.href = href;
    link.download = `toolkitly-${form.type}-barcode.${extension}`;
    link.click();
}

function downloadPng() {
    if (!canRender.value) {
        setStatus(validationMessage.value);
        return;
    }

    download(canvas.value.toDataURL('image/png'), 'png');
    setStatus('PNG downloaded.');
    recordToolEvent('barcode-generator', 'download', { format: 'png', type: form.type });
}

function downloadSvg() {
    if (!canRender.value) {
        setStatus(validationMessage.value);
        return;
    }

    try {
        const svg = bwipjs.toSVG(renderOptions.value);
        const blob = new Blob([svg], { type: 'image/svg+xml' });
        const url = URL.createObjectURL(blob);
        download(url, 'svg');
        URL.revokeObjectURL(url);
        setStatus('SVG downloaded.');
        recordToolEvent('barcode-generator', 'download', { format: 'svg', type: form.type });
    } catch (error) {
        setStatus(error.message || 'The SVG could not be created.');
    }
}

async function copyImage() {
    if (!canRender.value) {
        setStatus(validationMessage.value);
        return;
    }

    if (!navigator.clipboard || !window.ClipboardItem) {
        setStatus('Image copy is not supported in this browser.');
        return;
    }

    canvas.value.toBlob(async (blob) => {
        if (!blob) {
            setStatus('The image could not be prepared for copying.');
            return;
        }

        try {
            await navigator.clipboard.write([
                new ClipboardItem({ [blob.type]: blob }),
            ]);
            setStatus('Barcode image copied.');
            recordToolEvent('barcode-generator', 'copy', { format: 'png', type: form.type });
        } catch (error) {
            setStatus(error.message || 'The image could not be copied.');
        }
    });
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
        types.value = data.types || types.value;
        Object.assign(form, data.defaults || {});
    } catch {
        setStatus('Using local barcode settings.');
    }
}

async function validateOnServer() {
    if (!canRender.value) {
        return;
    }

    try {
        await fetch(props.payloadUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(form),
        });
    } catch {
        // Client-side generation remains available if the API is unreachable.
    }
}

watch(form, () => {
    scheduleRender();
    validateOnServer();
}, { deep: true });

watch(() => form.type, () => {
    form.text = selectedType.value.placeholder;
});

onMounted(async () => {
    await loadMetadata();
    await nextTick();
    render();
});
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_460px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">Free browser-based tool</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">Barcode Generator</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">
                    Create Code 128, EAN-13, and UPC-A barcodes for labels, inventory, product mockups, and internal tracking.
                </p>
            </div>

            <div class="grid gap-5">
                <div class="flex flex-wrap gap-2" aria-label="Barcode type">
                    <button
                        v-for="type in types"
                        :key="type.value"
                        type="button"
                        class="rounded-lg border px-3 py-2 text-sm font-semibold transition focus:outline-none focus:ring-4 focus:ring-[#2f7c67]/15"
                        :class="form.type === type.value ? 'border-[#2f7c67] bg-[#2f7c67] text-white' : 'border-[#cdd8d2] bg-white text-[#47534d] hover:border-[#6d8d80]'"
                        @click="form.type = type.value"
                    >
                        {{ type.label }}
                    </button>
                </div>

                <label class="grid gap-2" for="barcode-text">
                    <span class="text-sm font-medium text-[#2d2923]">Content</span>
                    <input
                        id="barcode-text"
                        v-model="form.text"
                        :placeholder="selectedType.placeholder"
                        maxlength="128"
                        class="h-12 rounded-lg border border-[#cdd8d2] bg-white px-4 text-base text-[#171411] shadow-sm outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15"
                    >
                    <span class="text-sm text-[#655d51]">{{ selectedType.help }}</span>
                </label>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="grid gap-2" for="barcode-scale">
                        <span class="text-sm font-medium text-[#2d2923]">Line width</span>
                        <input id="barcode-scale" v-model="form.scale" type="range" min="1" max="6" step="1" class="accent-[#2f7c67]">
                    </label>

                    <label class="grid gap-2" for="barcode-height">
                        <span class="text-sm font-medium text-[#2d2923]">Height</span>
                        <input id="barcode-height" v-model="form.height" type="range" min="8" max="40" step="1" class="accent-[#2f7c67]">
                    </label>
                </div>

                <label class="flex h-12 items-center gap-3 rounded-lg border border-[#cdd8d2] bg-white px-4 text-sm font-medium text-[#2d2923] md:max-w-sm">
                    <input v-model="form.include_text" type="checkbox" class="size-4 accent-[#2f7c67]">
                    Show readable text
                </label>
            </div>
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                <div class="grid min-h-72 place-items-center overflow-auto rounded-lg border border-[#dfe7e2] bg-[#f8faf8] p-4">
                    <canvas ref="canvas" class="max-w-full"></canvas>
                </div>

                <div class="mt-4 grid sm:grid-cols-2 gap-3">
                    <button type="button" class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus:outline-none focus:ring-4 focus:ring-[#101820]/20" @click="downloadPng">
                        PNG
                    </button>
                    <button type="button" class="rounded-lg border border-[#c6d4cd] px-4 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] hover:bg-[#f4f7f5] focus:outline-none focus:ring-4 focus:ring-[#101820]/10" @click="downloadSvg">
                        SVG
                    </button>
                    <button type="button" class="sm:col-span-2 rounded-lg border border-[#c6d4cd] px-4 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] hover:bg-[#f4f7f5] focus:outline-none focus:ring-4 focus:ring-[#101820]/10" @click="copyImage">
                        Copy image
                    </button>
                </div>

                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
