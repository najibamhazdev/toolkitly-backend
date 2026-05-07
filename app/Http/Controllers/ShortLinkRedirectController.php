<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use App\Services\ShortLink\ShortLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShortLinkRedirectController extends Controller
{
    public function __construct(private readonly ShortLinkService $shortLinks) {}

    public function __invoke(Request $request, string $code): RedirectResponse
    {
        $link = ShortLink::where('code', $code)->firstOrFail();

        abort_if($link->isExpired(), 404);

        $this->shortLinks->recordClick($link, $request);

        return redirect()->away($link->destination_url);
    }
}
