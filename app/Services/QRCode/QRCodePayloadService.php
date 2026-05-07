<?php

namespace App\Services\QRCode;

use InvalidArgumentException;

class QRCodePayloadService
{
    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function types(): array
    {
        return [
            ['value' => 'url', 'label' => 'URL'],
            ['value' => 'wifi', 'label' => 'WiFi'],
            ['value' => 'vcard', 'label' => 'Business card'],
            ['value' => 'text', 'label' => 'Text'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'type' => 'url',
            'url' => config('app.url').'/qr-code-generator',
            'text' => 'ToolKitly QR Code Generator',
            'wifi' => [
                'ssid' => '',
                'password' => '',
                'encryption' => 'WPA',
                'hidden' => false,
            ],
            'vcard' => [
                'first_name' => '',
                'last_name' => '',
                'organization' => '',
                'title' => '',
                'phone' => '',
                'email' => '',
                'website' => '',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function make(array $data): string
    {
        return match ($data['type'] ?? 'url') {
            'url' => trim((string) ($data['url'] ?? '')),
            'text' => trim((string) ($data['text'] ?? '')),
            'wifi' => $this->wifiPayload($data['wifi'] ?? []),
            'vcard' => $this->vCardPayload($data['vcard'] ?? []),
            default => throw new InvalidArgumentException('Unsupported QR code type.'),
        };
    }

    /**
     * @param  mixed  $wifi
     */
    private function wifiPayload(mixed $wifi): string
    {
        $wifi = is_array($wifi) ? $wifi : [];

        $ssid = $this->escapeWifi((string) ($wifi['ssid'] ?? ''));
        $password = $this->escapeWifi((string) ($wifi['password'] ?? ''));
        $encryption = in_array(($wifi['encryption'] ?? 'WPA'), ['WPA', 'WEP', 'nopass'], true)
            ? $wifi['encryption']
            : 'WPA';
        $hidden = ! empty($wifi['hidden']) ? 'true' : 'false';

        return "WIFI:T:{$encryption};S:{$ssid};P:{$password};H:{$hidden};;";
    }

    /**
     * @param  mixed  $vcard
     */
    private function vCardPayload(mixed $vcard): string
    {
        $vcard = is_array($vcard) ? $vcard : [];
        $firstName = trim((string) ($vcard['first_name'] ?? ''));
        $lastName = trim((string) ($vcard['last_name'] ?? ''));
        $fullName = trim($firstName.' '.$lastName);

        $lines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            'N:'.$this->escapeVCard($lastName).';'.$this->escapeVCard($firstName).';;;',
            'FN:'.$this->escapeVCard($fullName),
        ];

        foreach ([
            'organization' => 'ORG',
            'title' => 'TITLE',
            'phone' => 'TEL',
            'email' => 'EMAIL',
            'website' => 'URL',
        ] as $field => $label) {
            $value = trim((string) ($vcard[$field] ?? ''));

            if ($value !== '') {
                $lines[] = $label.':'.$this->escapeVCard($value);
            }
        }

        $lines[] = 'END:VCARD';

        return implode("\n", $lines);
    }

    private function escapeWifi(string $value): string
    {
        return str_replace(['\\', ';', ',', ':', '"'], ['\\\\', '\\;', '\\,', '\\:', '\\"'], $value);
    }

    private function escapeVCard(string $value): string
    {
        return str_replace(["\\", "\n", "\r", ';', ','], ['\\\\', '\\n', '', '\;', '\,'], $value);
    }
}
