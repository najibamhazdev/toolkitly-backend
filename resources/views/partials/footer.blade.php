<footer class="border-t border-[#d9e1dc] bg-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-[#5f574c] sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/toolkitly-logo.png') }}" alt="ToolKitly" class="h-9 w-36 object-contain object-left lg:h-12 lg:w-52">
            <p>&copy; {{ date('Y') }} ToolKitly. Free online tools for everyday digital work.</p>
        </div>
        <nav class="flex flex-wrap gap-4" aria-label="Footer navigation">
            <a class="hover:text-[#2f7c67]" href="{{ url('/about') }}">About</a>
            <a class="hover:text-[#2f7c67]" href="{{ url('/privacy-policy') }}">Privacy Policy</a>
            <a class="hover:text-[#2f7c67]" href="{{ url('/terms') }}">Terms</a>
            <a class="hover:text-[#2f7c67]" href="{{ url('/contact') }}">Contact</a>
            <a class="hover:text-[#2f7c67]" href="{{ url('/tools-sitemap') }}">Tools sitemap</a>
            <a class="hover:text-[#2f7c67]" href="{{ url('/sitemap.xml') }}">XML sitemap</a>
        </nav>
    </div>
</footer>
