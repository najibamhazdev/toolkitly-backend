import { createApp } from 'vue';

const mount = document.querySelector('#app');

if (mount) {
    const components = {
        'barcode-generator': () => import('./components/BarcodeGenerator.vue'),
        'favicon-generator': () => import('./components/FaviconGenerator.vue'),
        'image-compressor': () => import('./components/ImageCompressor.vue'),
        'image-converter': () => import('./components/ImageConverter.vue'),
        'image-resizer': () => import('./components/ImageResizer.vue'),
        'merge-pdf': () => import('./components/MergePdf.vue'),
        'pdf-utility': () => import('./components/PdfUtility.vue'),
        'qr-code-generator': () => import('./components/QRCodeGenerator.vue'),
        'short-links': () => import('./components/ShortLinks.vue'),
    };
    const loadComponent = components[mount.dataset.tool];

    if (loadComponent) {
        const { default: component } = await loadComponent();
        createApp(component, {
            metadataUrl: mount.dataset.metadataUrl,
            actionUrl: mount.dataset.actionUrl,
            toolKind: mount.dataset.toolKind,
            createUrl: mount.dataset.createUrl,
            mergeUrl: mount.dataset.mergeUrl,
            payloadUrl: mount.dataset.payloadUrl,
            initialUrl: mount.dataset.initialUrl,
        }).mount(mount);
    }
}
