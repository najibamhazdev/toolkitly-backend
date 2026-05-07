<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Image\ImageToolService;
use Illuminate\Http\JsonResponse;

class FaviconGeneratorController extends Controller
{
    public function __construct(private readonly ImageToolService $images) {}

    public function show(): JsonResponse
    {
        return response()->json($this->images->faviconMetadata());
    }
}
