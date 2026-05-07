# ToolKitly 🚀

**ToolKitly** is a free online digital toolbox built with  
:contentReference[oaicite:1]{index=1} + :contentReference[oaicite:2]{index=2} + Docker.

The goal of ToolKitly is to provide **fast, simple, and free online utilities** for developers, designers, businesses, students, and everyday users.

Revenue is generated through:

- Display ads
- Optional rewarded ads before downloads

No subscriptions. No premium plans. 100% free.

---

# Features

## Current Tools

### QR Code Tools

- QR Code Generator
- URL QR
- WiFi QR
- Text QR
- Business Card QR

Route:

```txt
/qr-code-generator
```

---

### Barcode Tools

- Code128
- EAN13
- UPC

Route:

```txt
/barcode-generator
```

---

### PDF Tools

- Merge PDF

Route:

```txt
/merge-pdf
```

---

### Image Tools

- Compress Image

Route:

```txt
/image-compressor
```

---

### Icon Tools

- Favicon Generator
- Apple Touch Icons
- Android Icons

Route:

```txt
/favicon-generator
```

---

### URL Tools

- Short Links
- Analytics Ready

Route:

```txt
/short-links
```

---

# Upcoming Tools

- Split PDF
- JPG to PDF
- PDF to Image
- OCR
- Image Resizer
- Image Converter
- Password Generator
- UUID Generator
- JSON Formatter
- Meta Tag Generator
- UTM Builder

---

# Tech Stack

## Backend

- :contentReference[oaicite:3]{index=3}
- PHP 8.3+
- REST API
- Service-oriented architecture

## Frontend

- :contentReference[oaicite:4]{index=4} 3
- Vite
- TailwindCSS

## Infrastructure

- Docker
- Laravel Sail
- MySQL
- Redis

---

# Project Structure

```txt
app/
├── Http/
├── Jobs/
├── Models/
└── Services/
    ├── QRCode/
    ├── Barcode/
    ├── Image/
    ├── Pdf/
    ├── OCR/
    └── ShortLink/
```

---

# Installation

## Clone Repository

```bash
git clone <your-repository-url>
cd toolkitly
```

---

## Start Docker Environment

```bash
docker compose up -d
```

or:

```bash
./vendor/bin/sail up -d
```

---

## Install Dependencies

### PHP

```bash
./vendor/bin/sail composer install
```

### Node

```bash
./vendor/bin/sail npm install
```

---

## Environment

Copy environment file:

```bash
cp .env.example .env
```

Generate app key:

```bash
./vendor/bin/sail artisan key:generate
```

---

## Database

Run migrations:

```bash
./vendor/bin/sail artisan migrate
```

---

## Start Frontend

```bash
./vendor/bin/sail npm run dev
```

---

# Local Development

Application:

```txt
http://localhost:8000
```

Mail:

```txt
http://localhost:8025
```

---

# Design Principles

ToolKitly follows:

- Mobile-first design
- SEO-first architecture
- Fast page loads
- Low infrastructure cost
- Queue-based heavy processing
- Temporary file cleanup

---

# Security

- Rate limiting
- File size restrictions
- Temporary file auto-deletion
- Input validation
- Queue isolation for heavy jobs

---

# Monetization Strategy

ToolKitly is free forever.

Revenue comes from:

- Google AdSense
- Rewarded video ads

No subscriptions.

---

# Vision

Build one of the best free online utility platforms with:

- Fast UX
- Useful tools
- Clean design
- Global accessibility

---

# License

MIT

---

Built with ❤️ by Najib