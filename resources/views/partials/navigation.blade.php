<header class="border-b border-[#d9e1dc] bg-white/90">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('tools.qr-code-generator') }}" class="flex items-center gap-3">
            <span class="grid size-9 place-items-center rounded-lg bg-[#101820] text-sm font-bold text-white">TK</span>
            <span class="text-lg font-semibold">ToolKitly</span>
        </a>

        @php
            $link = 'block rounded-lg px-3 py-2 text-sm font-medium text-[#47534d] transition hover:bg-[#f4f7f5] hover:text-[#171411]';
            $active = 'bg-[#2f7c67] text-white hover:bg-[#2f7c67] hover:text-white';
            $button = 'rounded-lg border border-[#cdd8d2] px-3 py-2 text-sm font-semibold text-[#47534d] transition hover:border-[#6d8d80] hover:text-[#171411]';
        @endphp

        <nav aria-label="Primary navigation" class="flex flex-wrap items-center justify-end gap-2">
            <a class="{{ $button }} {{ request()->routeIs('tools.qr-code-generator') ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}" href="{{ route('tools.qr-code-generator') }}">QR</a>
            <a class="{{ $button }} {{ request()->routeIs('tools.barcode-generator') ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}" href="{{ route('tools.barcode-generator') }}">Barcode</a>

            <details class="relative">
                <summary class="{{ $button }} list-none cursor-pointer {{ request()->routeIs('tools.merge-pdf') || request()->routeIs('tools.split-pdf') || request()->routeIs('tools.pdf-to-jpg') || request()->routeIs('tools.jpg-to-pdf') || request()->routeIs('tools.remove-pdf-pages') ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}">PDF</summary>
                <div class="absolute right-0 z-20 mt-2 grid min-w-48 gap-1 rounded-lg border border-[#cdd8d2] bg-white p-2 shadow-lg">
                    <a class="{{ $link }} {{ request()->routeIs('tools.merge-pdf') ? $active : '' }}" href="{{ route('tools.merge-pdf') }}">Merge PDF</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.split-pdf') ? $active : '' }}" href="{{ route('tools.split-pdf') }}">Split PDF</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.pdf-to-jpg') ? $active : '' }}" href="{{ route('tools.pdf-to-jpg') }}">PDF to JPG</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.jpg-to-pdf') ? $active : '' }}" href="{{ route('tools.jpg-to-pdf') }}">JPG to PDF</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.remove-pdf-pages') ? $active : '' }}" href="{{ route('tools.remove-pdf-pages') }}">Remove Pages</a>
                </div>
            </details>

            <details class="relative">
                <summary class="{{ $button }} list-none cursor-pointer {{ request()->routeIs('tools.image-*') || request()->routeIs('tools.favicon-generator') ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}">Images</summary>
                <div class="absolute right-0 z-20 mt-2 grid min-w-56 gap-1 rounded-lg border border-[#cdd8d2] bg-white p-2 shadow-lg">
                    <a class="{{ $link }} {{ request()->routeIs('tools.image-compressor') ? $active : '' }}" href="{{ route('tools.image-compressor') }}">Image Compressor</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.image-converter') ? $active : '' }}" href="{{ route('tools.image-converter') }}">Image Converter</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.image-resizer') ? $active : '' }}" href="{{ route('tools.image-resizer') }}">Image Resizer</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.favicon-generator') ? $active : '' }}" href="{{ route('tools.favicon-generator') }}">Favicon Generator</a>
                </div>
            </details>

            <a class="{{ $button }} {{ request()->routeIs('tools.short-links') ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}" href="{{ route('tools.short-links') }}">Short Links</a>
        </nav>
    </div>
</header>
