<?php

namespace App\Services\Barcode;

use InvalidArgumentException;

class BarcodePayloadService
{
    /**
     * @return array<int, array{value: string, label: string, bcid: string, placeholder: string, help: string}>
     */
    public function types(): array
    {
        return [
            [
                'value' => 'code128',
                'label' => 'Code 128',
                'bcid' => 'code128',
                'placeholder' => 'TK-2026-0001',
                'help' => 'Letters, numbers, spaces, and common symbols.',
            ],
            [
                'value' => 'ean13',
                'label' => 'EAN-13',
                'bcid' => 'ean13',
                'placeholder' => '5901234123457',
                'help' => '12 or 13 digits for retail product codes.',
            ],
            [
                'value' => 'upc',
                'label' => 'UPC-A',
                'bcid' => 'upca',
                'placeholder' => '042100005264',
                'help' => '11 or 12 digits for UPC-A product codes.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'type' => 'code128',
            'text' => 'TK-2026-0001',
            'scale' => 3,
            'height' => 12,
            'include_text' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function make(string $type, string $text): array
    {
        $text = trim($text);

        if ($text === '') {
            throw new InvalidArgumentException('Enter barcode content.');
        }

        return [
            'type' => $type,
            'bcid' => $this->bcid($type),
            'text' => $this->normalizeText($type, $text),
        ];
    }

    public function bcid(string $type): string
    {
        return match ($type) {
            'code128' => 'code128',
            'ean13' => 'ean13',
            'upc' => 'upca',
            default => throw new InvalidArgumentException('Unsupported barcode type.'),
        };
    }

    public function pattern(string $type): string
    {
        return match ($type) {
            'code128' => '.{1,128}',
            'ean13' => '\d{12,13}',
            'upc' => '\d{11,12}',
            default => throw new InvalidArgumentException('Unsupported barcode type.'),
        };
    }

    private function normalizeText(string $type, string $text): string
    {
        return match ($type) {
            'code128' => mb_substr($text, 0, 128),
            'ean13', 'upc' => preg_replace('/\D+/', '', $text) ?: '',
            default => throw new InvalidArgumentException('Unsupported barcode type.'),
        };
    }
}
