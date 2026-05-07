<?php

namespace App\Services\Image;

class ImageToolService
{
    /**
     * @return array<int, array{value: string, label: string, mime: string}>
     */
    public function outputFormats(): array
    {
        return [
            ['value' => 'jpeg', 'label' => 'JPG', 'mime' => 'image/jpeg'],
            ['value' => 'png', 'label' => 'PNG', 'mime' => 'image/png'],
            ['value' => 'webp', 'label' => 'WebP', 'mime' => 'image/webp'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function compressorMetadata(): array
    {
        return [
            'max_files' => 20,
            'max_upload_kb' => (int) config('toolkitly.max_upload_kb', 10240),
            'formats' => $this->outputFormats(),
            'defaults' => [
                'quality' => 72,
                'max_width' => 1920,
                'format' => 'jpeg',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function converterMetadata(): array
    {
        return [
            'max_files' => 20,
            'max_upload_kb' => (int) config('toolkitly.max_upload_kb', 10240),
            'formats' => $this->outputFormats(),
            'defaults' => [
                'format' => 'webp',
                'quality' => 90,
            ],
            'examples' => [
                ['from' => 'JPG', 'to' => 'PNG'],
                ['from' => 'PNG', 'to' => 'WebP'],
                ['from' => 'WebP', 'to' => 'JPG'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function faviconMetadata(): array
    {
        return [
            'max_upload_kb' => (int) config('toolkitly.max_upload_kb', 10240),
            'sizes' => [
                ['size' => 16, 'filename' => 'favicon-16x16.png'],
                ['size' => 32, 'filename' => 'favicon-32x32.png'],
                ['size' => 180, 'filename' => 'apple-touch-icon.png'],
                ['size' => 192, 'filename' => 'android-chrome-192x192.png'],
                ['size' => 512, 'filename' => 'android-chrome-512x512.png'],
            ],
            'ico_sizes' => [16, 32, 48],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function resizerMetadata(): array
    {
        return [
            'max_upload_kb' => (int) config('toolkitly.max_upload_kb', 10240),
            'formats' => $this->outputFormats(),
            'defaults' => [
                'format' => 'jpeg',
                'quality' => 88,
                'fit' => 'cover',
                'background' => '#ffffff',
            ],
            'presets' => [
                [
                    'group' => 'Instagram',
                    'items' => [
                        ['label' => 'Instagram Square Post', 'width' => 1080, 'height' => 1080],
                        ['label' => 'Instagram Portrait Post', 'width' => 1080, 'height' => 1350],
                        ['label' => 'Instagram Story/Reel', 'width' => 1080, 'height' => 1920],
                    ],
                ],
                [
                    'group' => 'Facebook',
                    'items' => [
                        ['label' => 'Facebook Post', 'width' => 1200, 'height' => 630],
                        ['label' => 'Facebook Cover', 'width' => 1640, 'height' => 924],
                        ['label' => 'Facebook Story', 'width' => 1080, 'height' => 1920],
                    ],
                ],
                [
                    'group' => 'LinkedIn',
                    'items' => [
                        ['label' => 'LinkedIn Post', 'width' => 1200, 'height' => 627],
                        ['label' => 'LinkedIn Company Cover', 'width' => 1128, 'height' => 191],
                        ['label' => 'LinkedIn Profile Banner', 'width' => 1584, 'height' => 396],
                    ],
                ],
            ],
        ];
    }
}
