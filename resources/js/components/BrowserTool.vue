<script setup>
import { computed, reactive, ref, watch } from 'vue';
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
    actionUrl: {
        type: String,
        default: '',
    },
});

const status = ref('');
const text = ref('');
const output = ref('');
const isChecking = ref(false);
const base64Mode = ref('encode');
const hashAlgorithm = ref('SHA-256');
const regexPattern = ref('\\btool\\w*');
const regexFlags = ref('gi');
const yamlMode = ref('yaml-to-json');
const uuidCount = ref(5);
const passwords = ref([]);
const password = reactive({
    length: 18,
    uppercase: true,
    lowercase: true,
    numbers: true,
    symbols: true,
});
const robots = reactive({
    userAgent: '*',
    disallow: '/admin\n/login',
    allow: '/',
    sitemap: 'https://example.com/sitemap.xml',
});
const meta = reactive({
    title: 'ToolKitly',
    description: 'Free online tools for everyday digital work.',
    url: 'https://toolkitly.net',
    image: 'https://toolkitly.net/og-image.jpg',
    type: 'website',
});
const sitemapUrls = ref('https://example.com/\nhttps://example.com/about\nhttps://example.com/contact');
const color = ref('#2f7c67');
const gradient = reactive({
    start: '#2f7c67',
    end: '#101820',
    angle: 135,
});
const shadow = reactive({
    x: 0,
    y: 12,
    blur: 32,
    spread: -12,
    opacity: 24,
});
const utm = reactive({
    url: 'https://example.com',
    source: 'google',
    medium: 'cpc',
    campaign: 'spring_launch',
    term: '',
    content: '',
});
const urlMode = ref('encode');
const timestamp = reactive({
    unix: Math.floor(Date.now() / 1000),
    date: new Date().toISOString().slice(0, 16),
});
const schema = reactive({
    type: 'Organization',
    name: 'ToolKitly',
    url: 'https://toolkitly.net',
    logo: 'https://toolkitly.net/logo.png',
});
const canonical = reactive({
    url: 'https://example.com/page',
});
const age = reactive({
    birthDate: '1990-01-01',
    compareDate: new Date().toISOString().slice(0, 10),
});
const dateCalc = reactive({
    start: new Date().toISOString().slice(0, 10),
    end: new Date(Date.now() + 7 * 86400000).toISOString().slice(0, 10),
    amount: 30,
});
const percentage = reactive({
    value: 20,
    total: 200,
    start: 100,
    end: 125,
});
const unit = reactive({
    category: 'length',
    value: 1,
    from: 'm',
    to: 'ft',
});
const token = reactive({
    length: 32,
    count: 5,
});

const successMessages = new Set([
    'JSON formatted.',
    'JSON minified.',
    'Encoded.',
    'Decoded.',
    'MD5 hash generated.',
    'SHA-256 hash generated.',
    'UUIDs generated.',
    'Passwords generated.',
    'URL encoded.',
    'URL decoded.',
    'Redirect chain ready.',
    'Result ready.',
    'SQL formatted.',
    'XML formatted.',
    'Converted.',
    'CSV converted.',
    'Case converted.',
    'Tokens generated.',
]);

const isDeveloper = computed(() => ['json-formatter', 'word-counter', 'case-converter', 'regex-tester', 'sql-formatter', 'xml-formatter', 'yaml-json-converter', 'csv-to-json', 'base64-tool', 'unix-timestamp', 'jwt-decoder', 'uuid-generator', 'password-generator', 'hash-generator', 'token-generator'].includes(props.toolKind));
const isSeo = computed(() => ['robots-txt-generator', 'sitemap-generator', 'meta-tag-generator', 'open-graph-preview', 'redirect-checker', 'http-header-checker', 'canonical-generator', 'schema-generator'].includes(props.toolKind));
const isDesign = computed(() => ['color-picker', 'css-gradient-generator', 'box-shadow-generator', 'age-calculator', 'date-calculator', 'percentage-calculator', 'unit-converter'].includes(props.toolKind));
const isUrl = computed(() => ['utm-builder', 'url-encoder', 'url-parser'].includes(props.toolKind));

const colorRgb = computed(() => {
    const { r, g, b } = hexToRgb(color.value);

    return `rgb(${r}, ${g}, ${b})`;
});

const colorHsl = computed(() => {
    const { r, g, b } = hexToRgb(color.value);
    const converted = rgbToHsl(r, g, b);

    return `hsl(${converted.h}, ${converted.s}%, ${converted.l}%)`;
});

const gradientCss = computed(() => `linear-gradient(${gradient.angle}deg, ${gradient.start}, ${gradient.end})`);
const boxShadowCss = computed(() => `${shadow.x}px ${shadow.y}px ${shadow.blur}px ${shadow.spread}px rgba(16, 24, 32, ${shadow.opacity / 100})`);

const wordStats = computed(() => {
    const words = text.value.trim() ? text.value.trim().split(/\s+/).length : 0;
    const characters = text.value.length;
    const charactersNoSpaces = text.value.replace(/\s/g, '').length;
    const sentences = text.value.trim() ? text.value.split(/[.!?]+/).filter((part) => part.trim()).length : 0;
    const paragraphs = text.value.trim() ? text.value.split(/\n\s*\n/).filter((part) => part.trim()).length : 0;

    return { words, characters, charactersNoSpaces, sentences, paragraphs };
});

const regexResult = computed(() => {
    try {
        const regex = new RegExp(regexPattern.value, regexFlags.value);
        const matches = Array.from(text.value.matchAll(regex)).map((match) => ({
            match: match[0],
            index: match.index,
            groups: match.slice(1),
        }));

        return JSON.stringify({ count: matches.length, matches }, null, 2);
    } catch (error) {
        return error.message || 'Invalid regular expression.';
    }
});

const timestampOutput = computed(() => {
    const dateFromUnix = new Date(Number(timestamp.unix) * 1000);
    const unixFromDate = Math.floor(new Date(timestamp.date).getTime() / 1000);

    return [
        `Unix to date: ${Number.isNaN(dateFromUnix.getTime()) ? 'Invalid timestamp' : dateFromUnix.toISOString()}`,
        `Date to Unix: ${Number.isNaN(unixFromDate) ? 'Invalid date' : unixFromDate}`,
    ].join('\n');
});

const jwtDecoded = computed(() => {
    const parts = text.value.split('.');

    if (parts.length < 2) {
        return 'Paste a JWT to decode its header and payload.';
    }

    try {
        return JSON.stringify({
            header: JSON.parse(base64UrlDecode(parts[0])),
            payload: JSON.parse(base64UrlDecode(parts[1])),
        }, null, 2);
    } catch {
        return 'This JWT could not be decoded.';
    }
});

const schemaMarkup = computed(() => JSON.stringify({
    '@context': 'https://schema.org',
    '@type': schema.type,
    name: schema.name,
    url: schema.url,
    logo: schema.logo,
}, null, 2));

const canonicalTag = computed(() => `<link rel="canonical" href="${escapeHtml(canonical.url)}">`);

const ageResult = computed(() => {
    const start = new Date(age.birthDate);
    const end = new Date(age.compareDate);

    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime()) || end < start) {
        return 'Enter valid dates.';
    }

    let years = end.getFullYear() - start.getFullYear();
    let months = end.getMonth() - start.getMonth();
    let days = end.getDate() - start.getDate();

    if (days < 0) {
        months -= 1;
        days += new Date(end.getFullYear(), end.getMonth(), 0).getDate();
    }

    if (months < 0) {
        years -= 1;
        months += 12;
    }

    return `${years} years, ${months} months, ${days} days`;
});

const percentageResult = computed(() => {
    const value = Number(percentage.value);
    const total = Number(percentage.total);
    const start = Number(percentage.start);
    const end = Number(percentage.end);
    const ofTotal = total === 0 ? 'Cannot divide by zero.' : `${value} is ${((value / total) * 100).toFixed(2)}% of ${total}`;
    const change = start === 0 ? 'Cannot calculate change from zero.' : `${start} to ${end} is ${(((end - start) / start) * 100).toFixed(2)}% change`;

    return `${ofTotal}\n${change}`;
});

const dateCalcResult = computed(() => {
    const start = new Date(dateCalc.start);
    const end = new Date(dateCalc.end);
    const amount = Number(dateCalc.amount) || 0;

    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
        return 'Enter valid dates.';
    }

    const plus = new Date(start);
    plus.setDate(plus.getDate() + amount);
    const minus = new Date(start);
    minus.setDate(minus.getDate() - amount);
    const diffDays = Math.round((end - start) / 86400000);

    return [
        `${amount} days after start: ${plus.toISOString().slice(0, 10)}`,
        `${amount} days before start: ${minus.toISOString().slice(0, 10)}`,
        `Difference: ${diffDays} days`,
    ].join('\n');
});

const unitResult = computed(() => {
    const value = Number(unit.value);

    if (Number.isNaN(value)) {
        return 'Enter a numeric value.';
    }

    if (unit.category === 'temperature') {
        const celsius = unit.from === 'c' ? value : unit.from === 'f' ? (value - 32) * 5 / 9 : value - 273.15;
        const converted = unit.to === 'c' ? celsius : unit.to === 'f' ? celsius * 9 / 5 + 32 : celsius + 273.15;

        return `${value} ${unit.from.toUpperCase()} = ${converted.toFixed(4)} ${unit.to.toUpperCase()}`;
    }

    const factors = unit.category === 'length'
        ? { m: 1, km: 1000, cm: 0.01, mm: 0.001, in: 0.0254, ft: 0.3048, yd: 0.9144, mi: 1609.344 }
        : { g: 1, kg: 1000, mg: 0.001, oz: 28.349523125, lb: 453.59237 };
    const converted = value * factors[unit.from] / factors[unit.to];

    return `${value} ${unit.from} = ${converted.toFixed(4)} ${unit.to}`;
});

const parsedUrl = computed(() => {
    try {
        const url = new URL(text.value);
        const params = Object.fromEntries(url.searchParams.entries());

        return JSON.stringify({
            protocol: url.protocol,
            username: url.username,
            password: url.password ? '[hidden]' : '',
            host: url.host,
            hostname: url.hostname,
            port: url.port,
            pathname: url.pathname,
            query: params,
            hash: url.hash,
        }, null, 2);
    } catch {
        return 'Enter a valid absolute URL.';
    }
});

const metaTags = computed(() => [
    `<title>${escapeHtml(meta.title)}</title>`,
    `<meta name="description" content="${escapeHtml(meta.description)}">`,
    `<link rel="canonical" href="${escapeHtml(meta.url)}">`,
    `<meta property="og:title" content="${escapeHtml(meta.title)}">`,
    `<meta property="og:description" content="${escapeHtml(meta.description)}">`,
    `<meta property="og:type" content="${escapeHtml(meta.type)}">`,
    `<meta property="og:url" content="${escapeHtml(meta.url)}">`,
    `<meta property="og:image" content="${escapeHtml(meta.image)}">`,
].join('\n'));

const robotsTxt = computed(() => {
    const lines = [`User-agent: ${robots.userAgent || '*'}`];
    robots.disallow.split('\n').map((line) => line.trim()).filter(Boolean).forEach((line) => lines.push(`Disallow: ${line}`));
    robots.allow.split('\n').map((line) => line.trim()).filter(Boolean).forEach((line) => lines.push(`Allow: ${line}`));

    if (robots.sitemap.trim()) {
        lines.push(`Sitemap: ${robots.sitemap.trim()}`);
    }

    return lines.join('\n');
});

const sitemapXml = computed(() => {
    const urls = sitemapUrls.value.split('\n').map((line) => line.trim()).filter(Boolean);
    const body = urls.map((url) => `  <url>\n    <loc>${escapeHtml(url)}</loc>\n  </url>`).join('\n');

    return `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${body}\n</urlset>`;
});

const utmUrl = computed(() => {
    try {
        const url = new URL(utm.url);
        const fields = {
            utm_source: utm.source,
            utm_medium: utm.medium,
            utm_campaign: utm.campaign,
            utm_term: utm.term,
            utm_content: utm.content,
        };

        Object.entries(fields).forEach(([key, value]) => {
            if (value.trim()) {
                url.searchParams.set(key, value.trim());
            }
        });

        return url.toString();
    } catch {
        return 'Enter a valid URL.';
    }
});

function setStatus(message) {
    status.value = message;

    if (successMessages.has(message)) {
        recordToolEvent(props.toolKind, 'generated', { mode: message.toLowerCase().replaceAll('.', '') });
    }
}

function formatJson(minify = false) {
    try {
        const parsed = JSON.parse(text.value);
        output.value = JSON.stringify(parsed, null, minify ? 0 : 2);
        setStatus(minify ? 'JSON minified.' : 'JSON formatted.');
    } catch (error) {
        output.value = '';
        setStatus(error.message || 'Invalid JSON.');
    }
}

function convertBase64() {
    try {
        output.value = base64Mode.value === 'encode'
            ? btoa(unescape(encodeURIComponent(text.value)))
            : decodeURIComponent(escape(atob(text.value)));
        setStatus(base64Mode.value === 'encode' ? 'Encoded.' : 'Decoded.');
    } catch {
        output.value = '';
        setStatus('This text could not be converted.');
    }
}

async function generateHash() {
    if (hashAlgorithm.value === 'MD5') {
        output.value = md5(text.value);
        setStatus('MD5 hash generated.');
        return;
    }

    const bytes = new TextEncoder().encode(text.value);
    const digest = await crypto.subtle.digest('SHA-256', bytes);
    output.value = Array.from(new Uint8Array(digest)).map((byte) => byte.toString(16).padStart(2, '0')).join('');
    setStatus('SHA-256 hash generated.');
}

function generateUuids() {
    output.value = Array.from({ length: Math.max(1, Math.min(100, Number(uuidCount.value) || 1)) }, () => crypto.randomUUID()).join('\n');
    setStatus('UUIDs generated.');
}

function generatePasswords() {
    const sets = [
        password.uppercase ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' : '',
        password.lowercase ? 'abcdefghijklmnopqrstuvwxyz' : '',
        password.numbers ? '0123456789' : '',
        password.symbols ? '!@#$%^&*()_+-=[]{};:,.?' : '',
    ].filter(Boolean);
    const chars = sets.join('') || 'abcdefghijklmnopqrstuvwxyz0123456789';
    const count = 5;
    const length = Math.max(8, Math.min(128, Number(password.length) || 18));

    passwords.value = Array.from({ length: count }, () => randomString(chars, length));
    output.value = passwords.value.join('\n');
    setStatus('Passwords generated.');
}

function convertUrl() {
    try {
        output.value = urlMode.value === 'encode' ? encodeURIComponent(text.value) : decodeURIComponent(text.value);
        setStatus(urlMode.value === 'encode' ? 'URL encoded.' : 'URL decoded.');
    } catch {
        output.value = '';
        setStatus('This URL text could not be converted.');
    }
}

async function checkRedirects() {
    if (!props.actionUrl || !text.value.trim()) {
        setStatus('Enter a URL to check.');
        return;
    }

    isChecking.value = true;
    output.value = '';
    setStatus('Checking redirects...');

    try {
        const response = await fetch(props.actionUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ url: text.value.trim() }),
        });
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'This URL could not be checked.');
        }

        output.value = JSON.stringify(data, null, 2);
        setStatus('Redirect chain ready.');
    } catch (error) {
        setStatus(error.message || 'This URL could not be checked.');
    } finally {
        isChecking.value = false;
    }
}

async function checkHeaders() {
    await checkHttpTool('Checking headers...');
}

async function checkHttpTool(message) {
    if (!props.actionUrl || !text.value.trim()) {
        setStatus('Enter a URL to check.');
        return;
    }

    isChecking.value = true;
    output.value = '';
    setStatus(message);

    try {
        const response = await fetch(props.actionUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ url: text.value.trim() }),
        });
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'This URL could not be checked.');
        }

        output.value = JSON.stringify(data, null, 2);
        setStatus('Result ready.');
    } catch (error) {
        setStatus(error.message || 'This URL could not be checked.');
    } finally {
        isChecking.value = false;
    }
}

function formatSql() {
    output.value = text.value
        .replace(/\s+/g, ' ')
        .replace(/\b(SELECT|FROM|WHERE|GROUP BY|ORDER BY|HAVING|LIMIT|JOIN|LEFT JOIN|RIGHT JOIN|INNER JOIN|OUTER JOIN|VALUES|SET)\b/gi, '\n$1')
        .replace(/\b(AND|OR)\b/gi, '\n  $1')
        .replace(/,/g, ',\n  ')
        .trim();
    setStatus('SQL formatted.');
}

function formatXml() {
    try {
        const parsed = new DOMParser().parseFromString(text.value, 'application/xml');

        if (parsed.querySelector('parsererror')) {
            throw new Error('Invalid XML.');
        }

        const serialized = new XMLSerializer().serializeToString(parsed);
        output.value = serialized.replace(/(>)(<)(\/*)/g, '$1\n$2$3').split('\n').reduce((lines, line) => {
            const trimmed = line.trim();
            const depth = trimmed.match(/^<\//) ? Math.max(lines.depth - 1, 0) : lines.depth;
            lines.items.push(`${'  '.repeat(depth)}${trimmed}`);
            lines.depth = trimmed.match(/^<[^!?/][^>]*[^/]>/) ? depth + 1 : depth;
            return lines;
        }, { depth: 0, items: [] }).items.join('\n');
        setStatus('XML formatted.');
    } catch (error) {
        output.value = '';
        setStatus(error.message || 'Invalid XML.');
    }
}

function convertYamlJson() {
    try {
        if (yamlMode.value === 'json-to-yaml') {
            output.value = objectToYaml(JSON.parse(text.value));
        } else {
            output.value = JSON.stringify(simpleYamlToObject(text.value), null, 2);
        }

        setStatus('Converted.');
    } catch (error) {
        output.value = '';
        setStatus(error.message || 'This content could not be converted.');
    }
}

function csvToJson() {
    const rows = text.value.trim().split(/\r?\n/).map(parseCsvLine);
    const headers = rows.shift() || [];
    output.value = JSON.stringify(rows.map((row) => Object.fromEntries(headers.map((header, index) => [header, row[index] ?? '']))), null, 2);
    setStatus('CSV converted.');
}

function convertCase(style) {
    const value = text.value;
    const words = value.toLowerCase().match(/[a-z0-9]+/gi) || [];

    output.value = {
        upper: value.toUpperCase(),
        lower: value.toLowerCase(),
        title: words.map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' '),
        sentence: value.toLowerCase().replace(/(^\s*\w|[.!?]\s*\w)/g, (match) => match.toUpperCase()),
        camel: words.map((word, index) => index === 0 ? word : word.charAt(0).toUpperCase() + word.slice(1)).join(''),
        kebab: words.join('-'),
        snake: words.join('_'),
    }[style] || value;
    setStatus('Case converted.');
}

function generateTokens() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const length = Math.max(8, Math.min(256, Number(token.length) || 32));
    const count = Math.max(1, Math.min(100, Number(token.count) || 5));

    output.value = Array.from({ length: count }, () => randomString(chars, length)).join('\n');
    setStatus('Tokens generated.');
}

function base64UrlDecode(value) {
    const padded = value.replace(/-/g, '+').replace(/_/g, '/').padEnd(Math.ceil(value.length / 4) * 4, '=');

    return decodeURIComponent(escape(atob(padded)));
}

function parseCsvLine(line) {
    const values = [];
    let current = '';
    let quoted = false;

    for (const char of line) {
        if (char === '"') {
            quoted = !quoted;
        } else if (char === ',' && !quoted) {
            values.push(current);
            current = '';
        } else {
            current += char;
        }
    }

    values.push(current);

    return values.map((value) => value.trim());
}

function simpleYamlToObject(value) {
    return Object.fromEntries(value.split(/\r?\n/)
        .map((line) => line.trim())
        .filter((line) => line && !line.startsWith('#'))
        .map((line) => {
            const [key, ...rest] = line.split(':');
            const raw = rest.join(':').trim();
            return [key.trim(), raw === 'true' ? true : raw === 'false' ? false : Number.isNaN(Number(raw)) ? raw : Number(raw)];
        }));
}

function objectToYaml(value, depth = 0) {
    if (Array.isArray(value)) {
        return value.map((item) => `${'  '.repeat(depth)}- ${typeof item === 'object' ? `\n${objectToYaml(item, depth + 1)}` : item}`).join('\n');
    }

    return Object.entries(value).map(([key, item]) => `${'  '.repeat(depth)}${key}: ${typeof item === 'object' && item !== null ? `\n${objectToYaml(item, depth + 1)}` : item}`).join('\n');
}

async function copy(value = output.value) {
    if (!value) {
        return;
    }

    await navigator.clipboard.writeText(value);
    setStatus('Copied.');
}

function hexToRgb(hex) {
    const normalized = hex.replace('#', '');
    const value = parseInt(normalized.length === 3 ? normalized.split('').map((char) => char + char).join('') : normalized, 16);

    return {
        r: (value >> 16) & 255,
        g: (value >> 8) & 255,
        b: value & 255,
    };
}

function rgbToHsl(r, g, b) {
    r /= 255;
    g /= 255;
    b /= 255;

    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    let h = 0;
    let s = 0;
    const l = (max + min) / 2;

    if (max !== min) {
        const d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        h = max === r ? (g - b) / d + (g < b ? 6 : 0) : max === g ? (b - r) / d + 2 : (r - g) / d + 4;
        h /= 6;
    }

    return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
}

function randomString(chars, length) {
    const values = new Uint32Array(length);
    crypto.getRandomValues(values);

    return Array.from(values, (value) => chars[value % chars.length]).join('');
}

function escapeHtml(value) {
    return String(value).replaceAll('&', '&amp;').replaceAll('"', '&quot;').replaceAll('<', '&lt;').replaceAll('>', '&gt;');
}

function md5(input) {
    function add32(a, b) { return (a + b) & 0xffffffff; }
    function cmn(q, a, b, x, s, t) { return add32(((add32(add32(a, q), add32(x, t)) << s) | (add32(add32(a, q), add32(x, t)) >>> (32 - s))), b); }
    function ff(a, b, c, d, x, s, t) { return cmn((b & c) | (~b & d), a, b, x, s, t); }
    function gg(a, b, c, d, x, s, t) { return cmn((b & d) | (c & ~d), a, b, x, s, t); }
    function hh(a, b, c, d, x, s, t) { return cmn(b ^ c ^ d, a, b, x, s, t); }
    function ii(a, b, c, d, x, s, t) { return cmn(c ^ (b | ~d), a, b, x, s, t); }
    function md5cycle(x, k) {
        let [a, b, c, d] = x;
        a = ff(a, b, c, d, k[0], 7, -680876936); d = ff(d, a, b, c, k[1], 12, -389564586); c = ff(c, d, a, b, k[2], 17, 606105819); b = ff(b, c, d, a, k[3], 22, -1044525330);
        a = ff(a, b, c, d, k[4], 7, -176418897); d = ff(d, a, b, c, k[5], 12, 1200080426); c = ff(c, d, a, b, k[6], 17, -1473231341); b = ff(b, c, d, a, k[7], 22, -45705983);
        a = ff(a, b, c, d, k[8], 7, 1770035416); d = ff(d, a, b, c, k[9], 12, -1958414417); c = ff(c, d, a, b, k[10], 17, -42063); b = ff(b, c, d, a, k[11], 22, -1990404162);
        a = ff(a, b, c, d, k[12], 7, 1804603682); d = ff(d, a, b, c, k[13], 12, -40341101); c = ff(c, d, a, b, k[14], 17, -1502002290); b = ff(b, c, d, a, k[15], 22, 1236535329);
        a = gg(a, b, c, d, k[1], 5, -165796510); d = gg(d, a, b, c, k[6], 9, -1069501632); c = gg(c, d, a, b, k[11], 14, 643717713); b = gg(b, c, d, a, k[0], 20, -373897302);
        a = gg(a, b, c, d, k[5], 5, -701558691); d = gg(d, a, b, c, k[10], 9, 38016083); c = gg(c, d, a, b, k[15], 14, -660478335); b = gg(b, c, d, a, k[4], 20, -405537848);
        a = gg(a, b, c, d, k[9], 5, 568446438); d = gg(d, a, b, c, k[14], 9, -1019803690); c = gg(c, d, a, b, k[3], 14, -187363961); b = gg(b, c, d, a, k[8], 20, 1163531501);
        a = gg(a, b, c, d, k[13], 5, -1444681467); d = gg(d, a, b, c, k[2], 9, -51403784); c = gg(c, d, a, b, k[7], 14, 1735328473); b = gg(b, c, d, a, k[12], 20, -1926607734);
        a = hh(a, b, c, d, k[5], 4, -378558); d = hh(d, a, b, c, k[8], 11, -2022574463); c = hh(c, d, a, b, k[11], 16, 1839030562); b = hh(b, c, d, a, k[14], 23, -35309556);
        a = hh(a, b, c, d, k[1], 4, -1530992060); d = hh(d, a, b, c, k[4], 11, 1272893353); c = hh(c, d, a, b, k[7], 16, -155497632); b = hh(b, c, d, a, k[10], 23, -1094730640);
        a = hh(a, b, c, d, k[13], 4, 681279174); d = hh(d, a, b, c, k[0], 11, -358537222); c = hh(c, d, a, b, k[3], 16, -722521979); b = hh(b, c, d, a, k[6], 23, 76029189);
        a = hh(a, b, c, d, k[9], 4, -640364487); d = hh(d, a, b, c, k[12], 11, -421815835); c = hh(c, d, a, b, k[15], 16, 530742520); b = hh(b, c, d, a, k[2], 23, -995338651);
        a = ii(a, b, c, d, k[0], 6, -198630844); d = ii(d, a, b, c, k[7], 10, 1126891415); c = ii(c, d, a, b, k[14], 15, -1416354905); b = ii(b, c, d, a, k[5], 21, -57434055);
        a = ii(a, b, c, d, k[12], 6, 1700485571); d = ii(d, a, b, c, k[3], 10, -1894986606); c = ii(c, d, a, b, k[10], 15, -1051523); b = ii(b, c, d, a, k[1], 21, -2054922799);
        a = ii(a, b, c, d, k[8], 6, 1873313359); d = ii(d, a, b, c, k[15], 10, -30611744); c = ii(c, d, a, b, k[6], 15, -1560198380); b = ii(b, c, d, a, k[13], 21, 1309151649);
        a = ii(a, b, c, d, k[4], 6, -145523070); d = ii(d, a, b, c, k[11], 10, -1120210379); c = ii(c, d, a, b, k[2], 15, 718787259); b = ii(b, c, d, a, k[9], 21, -343485551);
        x[0] = add32(a, x[0]); x[1] = add32(b, x[1]); x[2] = add32(c, x[2]); x[3] = add32(d, x[3]);
    }
    function md5blk(s) { const out = []; for (let i = 0; i < 64; i += 4) out[i >> 2] = s.charCodeAt(i) + (s.charCodeAt(i + 1) << 8) + (s.charCodeAt(i + 2) << 16) + (s.charCodeAt(i + 3) << 24); return out; }
    function rhex(n) { let s = ''; for (let j = 0; j < 4; j++) s += ((n >> (j * 8 + 4)) & 15).toString(16) + ((n >> (j * 8)) & 15).toString(16); return s; }
    const encoded = unescape(encodeURIComponent(input));
    let n = encoded.length;
    const state = [1732584193, -271733879, -1732584194, 271733878];
    let i;
    for (i = 64; i <= n; i += 64) md5cycle(state, md5blk(encoded.substring(i - 64, i)));
    let tail = Array(16).fill(0);
    const rest = encoded.substring(i - 64);
    for (i = 0; i < rest.length; i++) tail[i >> 2] |= rest.charCodeAt(i) << ((i % 4) << 3);
    tail[i >> 2] |= 0x80 << ((i % 4) << 3);
    if (i > 55) { md5cycle(state, tail); tail = Array(16).fill(0); }
    tail[14] = n * 8;
    md5cycle(state, tail);
    return state.map(rhex).join('');
}

watch(() => props.toolKind, () => {
    output.value = '';
    status.value = '';
}, { immediate: true });
</script>

<template>
    <main class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
        <section class="min-w-0">
            <div class="mb-6 max-w-3xl">
                <p class="mb-2 text-sm font-semibold text-[#7f5f2a]">{{ isDeveloper ? 'Text / Developer tool' : isSeo ? 'Web / SEO tool' : isDesign ? 'Color / Design tool' : 'URL tool' }}</p>
                <h1 class="text-3xl font-semibold tracking-normal text-[#171411] sm:text-4xl">{{ title }}</h1>
                <p class="mt-3 text-base leading-7 text-[#5f574c]">{{ description }}</p>
            </div>

            <div v-if="toolKind === 'open-graph-preview'" class="rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm">
                <div class="overflow-hidden rounded-lg border border-[#d9e1dc] bg-white">
                    <div class="grid aspect-[1.91/1] place-items-center bg-[#e8f0ec] px-4 text-center text-sm font-semibold text-[#47534d]">{{ meta.image }}</div>
                    <div class="p-4">
                        <p class="text-xs uppercase text-[#6b756f]">{{ meta.url }}</p>
                        <p class="mt-1 text-lg font-semibold text-[#171411]">{{ meta.title }}</p>
                        <p class="mt-1 text-sm text-[#5f574c]">{{ meta.description }}</p>
                    </div>
                </div>
            </div>

            <div v-else-if="toolKind === 'color-picker'" class="grid gap-4 rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm">
                <div class="h-48 rounded-lg border border-[#d9e1dc]" :style="{ backgroundColor: color }"></div>
                <input v-model="color" type="color" class="h-12 w-full rounded-lg border border-[#cdd8d2] bg-white">
                <pre class="overflow-auto rounded-lg bg-[#101820] p-4 text-sm text-white">{{ color.toUpperCase() + '\n' + colorRgb + '\n' + colorHsl }}</pre>
            </div>

            <div v-else-if="toolKind === 'css-gradient-generator'" class="grid gap-4 rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm">
                <div class="h-56 rounded-lg border border-[#d9e1dc]" :style="{ background: gradientCss }"></div>
                <pre class="overflow-auto rounded-lg bg-[#101820] p-4 text-sm text-white">background: {{ gradientCss }};</pre>
            </div>

            <div v-else-if="toolKind === 'box-shadow-generator'" class="grid min-h-80 place-items-center rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm">
                <div class="grid size-40 place-items-center rounded-lg bg-[#f8faf8] text-sm font-semibold text-[#47534d]" :style="{ boxShadow: boxShadowCss }">Preview</div>
                <pre class="mt-5 w-full overflow-auto rounded-lg bg-[#101820] p-4 text-sm text-white">box-shadow: {{ boxShadowCss }};</pre>
            </div>

            <div v-else class="grid gap-4">
                <textarea v-if="!['uuid-generator', 'password-generator', 'token-generator', 'robots-txt-generator', 'sitemap-generator', 'meta-tag-generator', 'schema-generator', 'canonical-generator', 'utm-builder', 'unix-timestamp', 'age-calculator', 'date-calculator', 'percentage-calculator', 'unit-converter'].includes(toolKind)" v-model="text" rows="12" class="w-full rounded-lg border border-[#cdd8d2] bg-white p-4 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15" placeholder="Paste text here"></textarea>
                <textarea v-if="toolKind === 'sitemap-generator'" v-model="sitemapUrls" rows="12" class="w-full rounded-lg border border-[#cdd8d2] bg-white p-4 text-sm text-[#171411] outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15"></textarea>
                <pre class="min-h-48 overflow-auto rounded-lg bg-[#101820] p-4 text-sm text-white">{{ toolKind === 'word-counter' ? JSON.stringify(wordStats, null, 2) : toolKind === 'regex-tester' ? regexResult : toolKind === 'unix-timestamp' ? timestampOutput : toolKind === 'jwt-decoder' ? jwtDecoded : toolKind === 'robots-txt-generator' ? robotsTxt : toolKind === 'sitemap-generator' ? sitemapXml : toolKind === 'meta-tag-generator' ? metaTags : toolKind === 'canonical-generator' ? canonicalTag : toolKind === 'schema-generator' ? schemaMarkup : toolKind === 'age-calculator' ? ageResult : toolKind === 'date-calculator' ? dateCalcResult : toolKind === 'percentage-calculator' ? percentageResult : toolKind === 'unit-converter' ? unitResult : toolKind === 'utm-builder' ? utmUrl : toolKind === 'url-parser' ? parsedUrl : output }}</pre>
            </div>
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                <div v-if="toolKind === 'json-formatter'" class="grid gap-3">
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="formatJson(false)">Format JSON</button>
                    <button class="rounded-lg border border-[#cdd8d2] px-4 py-3 text-sm font-semibold" type="button" @click="formatJson(true)">Minify JSON</button>
                </div>

                <div v-else-if="toolKind === 'word-counter'" class="grid sm:grid-cols-2 gap-3">
                    <div class="rounded-lg border border-[#cdd8d2] p-3"><p class="text-xs text-[#655d51]">Words</p><p class="text-2xl font-semibold">{{ wordStats.words }}</p></div>
                    <div class="rounded-lg border border-[#cdd8d2] p-3"><p class="text-xs text-[#655d51]">Characters</p><p class="text-2xl font-semibold">{{ wordStats.characters }}</p></div>
                    <div class="rounded-lg border border-[#cdd8d2] p-3"><p class="text-xs text-[#655d51]">Sentences</p><p class="text-2xl font-semibold">{{ wordStats.sentences }}</p></div>
                    <div class="rounded-lg border border-[#cdd8d2] p-3"><p class="text-xs text-[#655d51]">Paragraphs</p><p class="text-2xl font-semibold">{{ wordStats.paragraphs }}</p></div>
                </div>

                <div v-else-if="toolKind === 'case-converter'" class="grid gap-3">
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="convertCase('upper')">UPPERCASE</button>
                    <button class="rounded-lg border border-[#cdd8d2] px-4 py-3 text-sm font-semibold" type="button" @click="convertCase('lower')">lowercase</button>
                    <button class="rounded-lg border border-[#cdd8d2] px-4 py-3 text-sm font-semibold" type="button" @click="convertCase('title')">Title Case</button>
                    <button class="rounded-lg border border-[#cdd8d2] px-4 py-3 text-sm font-semibold" type="button" @click="convertCase('camel')">camelCase</button>
                    <button class="rounded-lg border border-[#cdd8d2] px-4 py-3 text-sm font-semibold" type="button" @click="convertCase('kebab')">kebab-case</button>
                    <button class="rounded-lg border border-[#cdd8d2] px-4 py-3 text-sm font-semibold" type="button" @click="convertCase('snake')">snake_case</button>
                </div>

                <div v-else-if="toolKind === 'regex-tester'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Pattern<input v-model="regexPattern" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Flags<input v-model="regexFlags" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <div v-else-if="toolKind === 'sql-formatter'" class="grid gap-3">
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="formatSql">Format SQL</button>
                </div>

                <div v-else-if="toolKind === 'xml-formatter'" class="grid gap-3">
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="formatXml">Format XML</button>
                </div>

                <div v-else-if="toolKind === 'yaml-json-converter'" class="grid gap-3">
                    <select v-model="yamlMode" class="h-11 rounded-lg border border-[#cdd8d2] px-3 text-sm"><option value="yaml-to-json">YAML to JSON</option><option value="json-to-yaml">JSON to YAML</option></select>
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="convertYamlJson">Convert</button>
                </div>

                <div v-else-if="toolKind === 'csv-to-json'" class="grid gap-3">
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="csvToJson">Convert CSV to JSON</button>
                </div>

                <div v-else-if="toolKind === 'base64-tool'" class="grid gap-3">
                    <select v-model="base64Mode" class="h-11 rounded-lg border border-[#cdd8d2] px-3 text-sm"><option value="encode">Encode</option><option value="decode">Decode</option></select>
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="convertBase64">Convert</button>
                </div>

                <div v-else-if="toolKind === 'unix-timestamp'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Unix timestamp<input v-model="timestamp.unix" type="number" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Date/time<input v-model="timestamp.date" type="datetime-local" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <div v-else-if="toolKind === 'jwt-decoder'" class="grid gap-3">
                    <p class="rounded-lg border border-[#cdd8d2] bg-[#f8faf8] p-3 text-sm text-[#655d51]">JWTs are decoded locally. Signatures are not verified here.</p>
                </div>

                <div v-else-if="toolKind === 'uuid-generator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Count<input v-model="uuidCount" type="number" min="1" max="100" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="generateUuids">Generate UUIDs</button>
                </div>

                <div v-else-if="toolKind === 'password-generator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Length<input v-model="password.length" type="range" min="8" max="64" class="accent-[#2f7c67]"><span>{{ password.length }} characters</span></label>
                    <label class="flex gap-2 text-sm"><input v-model="password.uppercase" type="checkbox"> Uppercase</label>
                    <label class="flex gap-2 text-sm"><input v-model="password.lowercase" type="checkbox"> Lowercase</label>
                    <label class="flex gap-2 text-sm"><input v-model="password.numbers" type="checkbox"> Numbers</label>
                    <label class="flex gap-2 text-sm"><input v-model="password.symbols" type="checkbox"> Symbols</label>
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="generatePasswords">Generate Passwords</button>
                </div>

                <div v-else-if="toolKind === 'hash-generator'" class="grid gap-3">
                    <select v-model="hashAlgorithm" class="h-11 rounded-lg border border-[#cdd8d2] px-3 text-sm"><option>SHA-256</option><option>MD5</option></select>
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="generateHash">Generate Hash</button>
                </div>

                <div v-else-if="toolKind === 'token-generator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Length<input v-model="token.length" type="number" min="8" max="256" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Count<input v-model="token.count" type="number" min="1" max="100" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="generateTokens">Generate Tokens</button>
                </div>

                <div v-else-if="toolKind === 'robots-txt-generator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">User agent<input v-model="robots.userAgent" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Disallow<textarea v-model="robots.disallow" rows="3" class="rounded-lg border border-[#cdd8d2] p-3"></textarea></label>
                    <label class="grid gap-2 text-sm font-medium">Allow<textarea v-model="robots.allow" rows="2" class="rounded-lg border border-[#cdd8d2] p-3"></textarea></label>
                    <label class="grid gap-2 text-sm font-medium">Sitemap<input v-model="robots.sitemap" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <div v-else-if="toolKind === 'meta-tag-generator' || toolKind === 'open-graph-preview'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Title<input v-model="meta.title" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Description<textarea v-model="meta.description" rows="3" class="rounded-lg border border-[#cdd8d2] p-3"></textarea></label>
                    <label class="grid gap-2 text-sm font-medium">URL<input v-model="meta.url" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Image URL<input v-model="meta.image" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <div v-else-if="toolKind === 'redirect-checker'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">URL<input v-model="text" placeholder="https://example.com" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white disabled:opacity-45" type="button" :disabled="isChecking" @click="checkRedirects">{{ isChecking ? 'Checking...' : 'Check redirects' }}</button>
                </div>

                <div v-else-if="toolKind === 'http-header-checker'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">URL<input v-model="text" placeholder="https://example.com" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white disabled:opacity-45" type="button" :disabled="isChecking" @click="checkHeaders">{{ isChecking ? 'Checking...' : 'Check headers' }}</button>
                </div>

                <div v-else-if="toolKind === 'canonical-generator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Canonical URL<input v-model="canonical.url" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <div v-else-if="toolKind === 'schema-generator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Type<select v-model="schema.type" class="h-11 rounded-lg border border-[#cdd8d2] px-3"><option>Organization</option><option>WebSite</option><option>Article</option><option>LocalBusiness</option></select></label>
                    <label class="grid gap-2 text-sm font-medium">Name<input v-model="schema.name" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">URL<input v-model="schema.url" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Logo / image<input v-model="schema.logo" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <div v-else-if="toolKind === 'css-gradient-generator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Start<input v-model="gradient.start" type="color" class="h-11"></label>
                    <label class="grid gap-2 text-sm font-medium">End<input v-model="gradient.end" type="color" class="h-11"></label>
                    <label class="grid gap-2 text-sm font-medium">Angle {{ gradient.angle }}deg<input v-model="gradient.angle" type="range" min="0" max="360" class="accent-[#2f7c67]"></label>
                </div>

                <div v-else-if="toolKind === 'box-shadow-generator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">X {{ shadow.x }}px<input v-model="shadow.x" type="range" min="-80" max="80" class="accent-[#2f7c67]"></label>
                    <label class="grid gap-2 text-sm font-medium">Y {{ shadow.y }}px<input v-model="shadow.y" type="range" min="-80" max="80" class="accent-[#2f7c67]"></label>
                    <label class="grid gap-2 text-sm font-medium">Blur {{ shadow.blur }}px<input v-model="shadow.blur" type="range" min="0" max="120" class="accent-[#2f7c67]"></label>
                    <label class="grid gap-2 text-sm font-medium">Spread {{ shadow.spread }}px<input v-model="shadow.spread" type="range" min="-80" max="80" class="accent-[#2f7c67]"></label>
                    <label class="grid gap-2 text-sm font-medium">Opacity {{ shadow.opacity }}%<input v-model="shadow.opacity" type="range" min="0" max="100" class="accent-[#2f7c67]"></label>
                </div>

                <div v-else-if="toolKind === 'age-calculator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Birth date<input v-model="age.birthDate" type="date" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Compare date<input v-model="age.compareDate" type="date" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <div v-else-if="toolKind === 'date-calculator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Start date<input v-model="dateCalc.start" type="date" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">End date<input v-model="dateCalc.end" type="date" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Days to add/subtract<input v-model="dateCalc.amount" type="number" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <div v-else-if="toolKind === 'percentage-calculator'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Value<input v-model="percentage.value" type="number" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Total<input v-model="percentage.total" type="number" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Start value<input v-model="percentage.start" type="number" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">End value<input v-model="percentage.end" type="number" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <div v-else-if="toolKind === 'unit-converter'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">Category<select v-model="unit.category" class="h-11 rounded-lg border border-[#cdd8d2] px-3"><option value="length">Length</option><option value="weight">Weight</option><option value="temperature">Temperature</option></select></label>
                    <label class="grid gap-2 text-sm font-medium">Value<input v-model="unit.value" type="number" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">From<input v-model="unit.from" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">To<input v-model="unit.to" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <p class="rounded-lg border border-[#cdd8d2] bg-[#f8faf8] p-3 text-sm text-[#655d51]">Length: m, km, cm, mm, in, ft, yd, mi. Weight: g, kg, mg, oz, lb. Temperature: c, f, k.</p>
                </div>

                <div v-else-if="toolKind === 'utm-builder'" class="grid gap-3">
                    <label class="grid gap-2 text-sm font-medium">URL<input v-model="utm.url" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Source<input v-model="utm.source" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Medium<input v-model="utm.medium" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Campaign<input v-model="utm.campaign" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Term<input v-model="utm.term" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                    <label class="grid gap-2 text-sm font-medium">Content<input v-model="utm.content" class="h-11 rounded-lg border border-[#cdd8d2] px-3"></label>
                </div>

                <div v-else-if="toolKind === 'url-encoder'" class="grid gap-3">
                    <select v-model="urlMode" class="h-11 rounded-lg border border-[#cdd8d2] px-3 text-sm"><option value="encode">Encode</option><option value="decode">Decode</option></select>
                    <button class="rounded-lg bg-[#101820] px-4 py-3 text-sm font-semibold text-white" type="button" @click="convertUrl">Convert</button>
                </div>

                <div v-else-if="toolKind === 'url-parser'" class="grid gap-3">
                    <p class="rounded-lg border border-[#cdd8d2] bg-[#f8faf8] p-3 text-sm text-[#655d51]">Paste an absolute URL in the editor to see its parsed parts.</p>
                </div>

                <button class="mt-4 w-full rounded-lg border border-[#2f7c67] px-4 py-3 text-sm font-semibold text-[#2f7c67]" type="button" @click="copy(toolKind === 'word-counter' ? JSON.stringify(wordStats, null, 2) : toolKind === 'regex-tester' ? regexResult : toolKind === 'unix-timestamp' ? timestampOutput : toolKind === 'jwt-decoder' ? jwtDecoded : toolKind === 'robots-txt-generator' ? robotsTxt : toolKind === 'sitemap-generator' ? sitemapXml : toolKind === 'meta-tag-generator' ? metaTags : toolKind === 'canonical-generator' ? canonicalTag : toolKind === 'schema-generator' ? schemaMarkup : toolKind === 'age-calculator' ? ageResult : toolKind === 'date-calculator' ? dateCalcResult : toolKind === 'percentage-calculator' ? percentageResult : toolKind === 'unit-converter' ? unitResult : toolKind === 'utm-builder' ? utmUrl : toolKind === 'url-parser' ? parsedUrl : toolKind === 'css-gradient-generator' ? `background: ${gradientCss};` : toolKind === 'box-shadow-generator' ? `box-shadow: ${boxShadowCss};` : toolKind === 'color-picker' ? `${color.toUpperCase()}\n${colorRgb}\n${colorHsl}` : output)">
                    Copy result
                </button>
                <p class="mt-3 min-h-6 text-sm text-[#655d51]" role="status" aria-live="polite">{{ status }}</p>
            </div>
        </aside>
    </main>
</template>
