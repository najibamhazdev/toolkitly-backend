<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { recordToolEvent } from '../analytics';

const props = defineProps({
    metadataUrl: {
        type: String,
        required: true,
    },
    createUrl: {
        type: String,
        required: true,
    },
});

const form = reactive({
    destination_url: '',
    expiration: '',
});
const options = ref([
    { value: '', label: 'Never' },
    { value: '1_day', label: '1 day' },
    { value: '7_days', label: '7 days' },
    { value: '30_days', label: '30 days' },
]);
const result = ref(null);
const status = ref('');
const isCreating = ref(false);

const canCreate = computed(() => form.destination_url.trim() !== '' && !isCreating.value);

function setStatus(message) {
    status.value = message;
}

function formatDate(value) {
    if (!value) {
        return 'Never';
    }

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

async function createLink() {
    if (!canCreate.value) {
        setStatus('Enter a URL to shorten.');
        return;
    }

    isCreating.value = true;
    result.value = null;
    setStatus('Creating short link...');

    try {
        const response = await fetch(props.createUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(form),
        });
        const data = await response.json();

        if (!response.ok) {
            const message = data.message || data.errors?.destination_url?.[0] || 'This short link could not be created.';
            throw new Error(message);
        }

        result.value = data.link;
        setStatus('Short link ready.');
        recordToolEvent('short-links', 'generated', { format: 'url' });
    } catch (error) {
        setStatus(error.message || 'This short link could not be created.');
    } finally {
        isCreating.value = false;
    }
}

async function copyLink() {
    if (!result.value?.short_url) {
        return;
    }

    try {
        await navigator.clipboard.writeText(result.value.short_url);
        setStatus('Short link copied.');
    } catch {
        setStatus('Copy is not supported in this browser.');
    }
}

async function refreshStats() {
    if (!result.value?.code) {
        return;
    }

    try {
        const response = await fetch(`${props.createUrl}/${result.value.code}`, {
            headers: { Accept: 'application/json' },
        });

        if (response.ok) {
            const data = await response.json();
            result.value = data.link;
            setStatus('Stats refreshed.');
        }
    } catch {
        setStatus('Stats could not be refreshed.');
    }
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
        options.value = data.expiration_options || options.value;
    } catch {
        setStatus('Using local short link settings.');
    }
}

onMounted(loadMetadata);
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">Free server-side tool</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">Short Links</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">
                    Create clean ToolKitly short links with click tracking and optional expiration.
                </p>
            </div>

            <form class="grid gap-4" @submit.prevent="createLink">
                <label class="grid gap-2" for="destination-url">
                    <span class="text-sm font-medium text-[#2d2923]">Destination URL</span>
                    <input
                        id="destination-url"
                        v-model="form.destination_url"
                        type="url"
                        placeholder="https://example.com/long-link"
                        maxlength="2048"
                        class="h-12 rounded-lg border border-[#cdd8d2] bg-white px-4 text-base text-[#171411] shadow-sm outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15"
                    >
                </label>

                <label class="grid gap-2 md:max-w-sm" for="expiration">
                    <span class="text-sm font-medium text-[#2d2923]">Expiration</span>
                    <select id="expiration" v-model="form.expiration" class="h-11 rounded-lg border border-[#cdd8d2] bg-white px-3 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                        <option v-for="option in options" :key="option.value" :value="option.value">{{ option.label }}</option>
                    </select>
                </label>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus:outline-none focus:ring-4 focus:ring-[#101820]/20 disabled:cursor-not-allowed disabled:opacity-45 md:w-fit"
                    :disabled="!canCreate"
                >
                    {{ isCreating ? 'Creating...' : 'Create short link' }}
                </button>
            </form>
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                <template v-if="result">
                    <p class="text-sm font-semibold text-[#171411]">Your short link</p>
                    <a :href="result.short_url" target="_blank" rel="noopener noreferrer" class="mt-2 block break-all rounded-lg border border-[#dfe7e2] bg-[#f8faf8] px-3 py-3 text-sm font-semibold text-[#2f7c67]">
                        {{ result.short_url }}
                    </a>

                    <div class="mt-4 grid sm:grid-cols-2 gap-3">
                        <button type="button" class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#26313b] focus:outline-none focus:ring-4 focus:ring-[#101820]/20" @click="copyLink">
                            Copy
                        </button>
                        <button type="button" class="rounded-lg border border-[#c6d4cd] px-4 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] hover:bg-[#f4f7f5] focus:outline-none focus:ring-4 focus:ring-[#101820]/10" @click="refreshStats">
                            Refresh
                        </button>
                    </div>

                    <dl class="mt-4 grid gap-3 rounded-lg border border-[#dfe7e2] bg-[#f8faf8] p-4 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-[#655d51]">Clicks</dt>
                            <dd class="font-semibold text-[#171411]">{{ result.clicks }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-[#655d51]">Expires</dt>
                            <dd class="text-right font-semibold text-[#171411]">{{ formatDate(result.expires_at) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-[#655d51]">Last click</dt>
                            <dd class="text-right font-semibold text-[#171411]">{{ formatDate(result.last_clicked_at) }}</dd>
                        </div>
                    </dl>
                </template>

                <p v-else class="rounded-lg border border-[#dfe7e2] bg-[#f8faf8] p-4 text-sm text-[#655d51]">
                    Create a short link to see the URL and click stats here.
                </p>

                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
