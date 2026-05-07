<script setup>
import QRCode from 'qrcode';
import { computed, nextTick, onMounted, reactive, ref, watch } from 'vue';

const props = defineProps({
    metadataUrl: {
        type: String,
        required: true,
    },
    payloadUrl: {
        type: String,
        required: true,
    },
    initialUrl: {
        type: String,
        required: true,
    },
});

const canvas = ref(null);
const status = ref('');
const types = ref([
    { value: 'url', label: 'URL' },
    { value: 'wifi', label: 'WiFi' },
    { value: 'vcard', label: 'Business card' },
    { value: 'text', label: 'Text' },
]);

const form = reactive({
    type: 'url',
    url: props.initialUrl,
    text: 'ToolKitly QR Code Generator',
    wifi: {
        ssid: '',
        password: '',
        encryption: 'WPA',
        hidden: false,
    },
    vcard: {
        first_name: '',
        last_name: '',
        organization: '',
        title: '',
        phone: '',
        email: '',
        website: '',
    },
});

const style = reactive({
    size: 360,
    margin: 2,
    errorCorrectionLevel: 'M',
    dark: '#101820',
    light: '#ffffff',
});

let renderHandle;

const csrfToken = computed(() => document.querySelector('meta[name="csrf-token"]')?.content || '');

const qrOptions = computed(() => ({
    width: Number(style.size),
    margin: Number(style.margin),
    errorCorrectionLevel: style.errorCorrectionLevel,
    color: {
        dark: style.dark,
        light: style.light,
    },
}));

const activePayload = computed(() => makePayload(form));

const hasPayload = computed(() => activePayload.value.trim().length > 0);

function makePayload(data) {
    if (data.type === 'url') {
        return data.url.trim();
    }

    if (data.type === 'text') {
        return data.text.trim();
    }

    if (data.type === 'wifi') {
        const ssid = escapeWifi(data.wifi.ssid);
        const password = escapeWifi(data.wifi.password);
        const encryption = data.wifi.encryption;
        const hidden = data.wifi.hidden ? 'true' : 'false';

        return `WIFI:T:${encryption};S:${ssid};P:${password};H:${hidden};;`;
    }

    const firstName = data.vcard.first_name.trim();
    const lastName = data.vcard.last_name.trim();
    const fullName = `${firstName} ${lastName}`.trim();
    const lines = [
        'BEGIN:VCARD',
        'VERSION:3.0',
        `N:${escapeVCard(lastName)};${escapeVCard(firstName)};;;`,
        `FN:${escapeVCard(fullName)}`,
    ];

    [
        ['organization', 'ORG'],
        ['title', 'TITLE'],
        ['phone', 'TEL'],
        ['email', 'EMAIL'],
        ['website', 'URL'],
    ].forEach(([field, label]) => {
        const value = data.vcard[field].trim();

        if (value) {
            lines.push(`${label}:${escapeVCard(value)}`);
        }
    });

    lines.push('END:VCARD');

    return lines.join('\n');
}

function escapeWifi(value) {
    return value.replaceAll('\\', '\\\\')
        .replaceAll(';', '\\;')
        .replaceAll(',', '\\,')
        .replaceAll(':', '\\:')
        .replaceAll('"', '\\"');
}

function escapeVCard(value) {
    return value.replaceAll('\\', '\\\\')
        .replaceAll('\n', '\\n')
        .replaceAll('\r', '')
        .replaceAll(';', '\\;')
        .replaceAll(',', '\\,');
}

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

    if (!hasPayload.value) {
        canvas.value.getContext('2d').clearRect(0, 0, canvas.value.width, canvas.value.height);
        setStatus('Enter content to generate a QR code.');
        return;
    }

    try {
        await QRCode.toCanvas(canvas.value, activePayload.value, qrOptions.value);
        setStatus(`${activePayload.value.length.toLocaleString()} characters encoded.`);
    } catch (error) {
        setStatus(error.message || 'This QR code could not be generated.');
    }
}

function download(href, extension) {
    const link = document.createElement('a');
    link.href = href;
    link.download = `toolkitly-${form.type}-qr-code.${extension}`;
    link.click();
}

function downloadPng() {
    if (!hasPayload.value) {
        setStatus('Enter content before downloading.');
        return;
    }

    download(canvas.value.toDataURL('image/png'), 'png');
    setStatus('PNG downloaded.');
}

async function downloadSvg() {
    if (!hasPayload.value) {
        setStatus('Enter content before downloading.');
        return;
    }

    try {
        const svg = await QRCode.toString(activePayload.value, {
            ...qrOptions.value,
            type: 'svg',
        });
        const blob = new Blob([svg], { type: 'image/svg+xml' });
        const url = URL.createObjectURL(blob);
        download(url, 'svg');
        URL.revokeObjectURL(url);
        setStatus('SVG downloaded.');
    } catch (error) {
        setStatus(error.message || 'The SVG could not be created.');
    }
}

async function copyImage() {
    if (!hasPayload.value) {
        setStatus('Enter content before copying.');
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
            setStatus('QR image copied.');
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
        form.url = props.initialUrl;
    } catch {
        setStatus('Using local QR settings.');
    }
}

async function validateOnServer() {
    try {
        await fetch(props.payloadUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                ...(csrfToken.value ? { 'X-CSRF-TOKEN': csrfToken.value } : {}),
            },
            body: JSON.stringify(form),
        });
    } catch {
        // Client-side generation remains available if the API is unreachable.
    }
}

watch([form, style], () => {
    scheduleRender();
    validateOnServer();
}, { deep: true });

onMounted(async () => {
    await loadMetadata();
    await nextTick();
    render();
});
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">Free browser-based tool</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">QR Code Generator</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">
                    Create QR codes for links, Wi-Fi networks, business cards, and text. Export clean PNG or SVG files instantly.
                </p>
            </div>

            <div class="grid gap-5">
                <div class="flex flex-wrap gap-2" aria-label="QR code type">
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

                <div class="grid gap-4">
                    <label v-if="form.type === 'url'" class="grid gap-2" for="qr-url">
                        <span class="text-sm font-medium text-[#2d2923]">URL</span>
                        <input id="qr-url" v-model="form.url" type="url" maxlength="2000" class="h-12 rounded-lg border border-[#cdd8d2] bg-white px-4 text-base text-[#171411] shadow-sm outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                    </label>

                    <label v-if="form.type === 'text'" class="grid gap-2" for="qr-text">
                        <span class="text-sm font-medium text-[#2d2923]">Text</span>
                        <textarea id="qr-text" v-model="form.text" maxlength="2000" class="min-h-40 resize-y rounded-lg border border-[#cdd8d2] bg-white px-4 py-3 text-base leading-6 text-[#171411] shadow-sm outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15"></textarea>
                    </label>

                    <div v-if="form.type === 'wifi'" class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2" for="qr-wifi-ssid">
                            <span class="text-sm font-medium text-[#2d2923]">Network name</span>
                            <input id="qr-wifi-ssid" v-model="form.wifi.ssid" maxlength="128" class="h-12 rounded-lg border border-[#cdd8d2] bg-white px-4 text-base text-[#171411] shadow-sm outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                        </label>

                        <label class="grid gap-2" for="qr-wifi-password">
                            <span class="text-sm font-medium text-[#2d2923]">Password</span>
                            <input id="qr-wifi-password" v-model="form.wifi.password" maxlength="128" class="h-12 rounded-lg border border-[#cdd8d2] bg-white px-4 text-base text-[#171411] shadow-sm outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                        </label>

                        <label class="grid gap-2" for="qr-wifi-encryption">
                            <span class="text-sm font-medium text-[#2d2923]">Security</span>
                            <select id="qr-wifi-encryption" v-model="form.wifi.encryption" class="h-12 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                                <option value="WPA">WPA/WPA2</option>
                                <option value="WEP">WEP</option>
                                <option value="nopass">No password</option>
                            </select>
                        </label>

                        <label class="flex h-12 items-center gap-3 self-end rounded-lg border border-[#cdd8d2] bg-white px-4 text-sm font-medium text-[#2d2923]">
                            <input v-model="form.wifi.hidden" type="checkbox" class="size-4 accent-[#2f7c67]">
                            Hidden network
                        </label>
                    </div>

                    <div v-if="form.type === 'vcard'" class="grid gap-4 md:grid-cols-2">
                        <label v-for="field in [
                            ['first_name', 'First name'],
                            ['last_name', 'Last name'],
                            ['organization', 'Organization'],
                            ['title', 'Title'],
                            ['phone', 'Phone'],
                            ['email', 'Email'],
                            ['website', 'Website'],
                        ]" :key="field[0]" class="grid gap-2" :for="`qr-vcard-${field[0]}`">
                            <span class="text-sm font-medium text-[#2d2923]">{{ field[1] }}</span>
                            <input :id="`qr-vcard-${field[0]}`" v-model="form.vcard[field[0]]" class="h-12 rounded-lg border border-[#cdd8d2] bg-white px-4 text-base text-[#171411] shadow-sm outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                        </label>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="grid gap-2" for="qr-size">
                        <span class="text-sm font-medium text-[#2d2923]">Size</span>
                        <input id="qr-size" v-model="style.size" type="range" min="160" max="800" step="20" class="accent-[#2f7c67]">
                    </label>

                    <label class="grid gap-2" for="qr-margin">
                        <span class="text-sm font-medium text-[#2d2923]">Quiet zone</span>
                        <input id="qr-margin" v-model="style.margin" type="range" min="0" max="8" step="1" class="accent-[#2f7c67]">
                    </label>

                    <label class="grid gap-2" for="qr-error">
                        <span class="text-sm font-medium text-[#2d2923]">Error correction</span>
                        <select id="qr-error" v-model="style.errorCorrectionLevel" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                            <option value="L">Low</option>
                            <option value="M">Medium</option>
                            <option value="Q">Quartile</option>
                            <option value="H">High</option>
                        </select>
                    </label>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="grid gap-2" for="qr-dark">
                            <span class="text-sm font-medium text-[#2d2923]">Code</span>
                            <input id="qr-dark" v-model="style.dark" type="color" class="h-11 w-full rounded-lg border border-[#cdd8d2] bg-white p-1">
                        </label>

                        <label class="grid gap-2" for="qr-light">
                            <span class="text-sm font-medium text-[#2d2923]">Background</span>
                            <input id="qr-light" v-model="style.light" type="color" class="h-11 w-full rounded-lg border border-[#cdd8d2] bg-white p-1">
                        </label>
                    </div>
                </div>
            </div>
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                <div class="grid aspect-square place-items-center overflow-hidden rounded-lg border border-[#dfe7e2] bg-[#f8faf8] p-4">
                    <canvas ref="canvas" class="max-h-full max-w-full"></canvas>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <button type="button" class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus:outline-none focus:ring-4 focus:ring-[#101820]/20" @click="downloadPng">
                        PNG
                    </button>
                    <button type="button" class="rounded-lg border border-[#c6d4cd] px-4 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] hover:bg-[#f4f7f5] focus:outline-none focus:ring-4 focus:ring-[#101820]/10" @click="downloadSvg">
                        SVG
                    </button>
                    <button type="button" class="col-span-2 rounded-lg border border-[#c6d4cd] px-4 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] hover:bg-[#f4f7f5] focus:outline-none focus:ring-4 focus:ring-[#101820]/10" @click="copyImage">
                        Copy image
                    </button>
                </div>

                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
