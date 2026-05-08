<?php

use App\Http\Controllers\Api\BarcodeController;
use App\Http\Controllers\Api\FaviconGeneratorController;
use App\Http\Controllers\Api\HttpHeaderCheckerController;
use App\Http\Controllers\Api\ImageCompressorController;
use App\Http\Controllers\Api\ImageConverterController;
use App\Http\Controllers\Api\ImageResizerController;
use App\Http\Controllers\Api\MergePdfController;
use App\Http\Controllers\Api\PdfToolController;
use App\Http\Controllers\Api\QRCodeController;
use App\Http\Controllers\Api\RedirectCheckerController;
use App\Http\Controllers\Api\ShortLinkController;
use App\Http\Controllers\Api\ToolEventController;
use Illuminate\Support\Facades\Route;

Route::prefix('tools/qr-code-generator')->name('api.tools.qr-code-generator.')->group(function (): void {
    Route::get('/', [QRCodeController::class, 'show'])->name('show');
    Route::post('/payload', [QRCodeController::class, 'payload'])->name('payload');
});

Route::prefix('tools/barcode-generator')->name('api.tools.barcode-generator.')->group(function (): void {
    Route::get('/', [BarcodeController::class, 'show'])->name('show');
    Route::post('/payload', [BarcodeController::class, 'payload'])->name('payload');
});

Route::prefix('tools/pdf/merge-pdf')->name('api.tools.merge-pdf.')->group(function (): void {
    Route::get('/', [MergePdfController::class, 'show'])->name('show');
    Route::post('/merge', [MergePdfController::class, 'merge'])->name('merge');
});

Route::prefix('tools/pdf/{tool}')->name('api.tools.pdf.')->group(function (): void {
    Route::get('/', [PdfToolController::class, 'show'])->name('show');
    Route::post('/process', [PdfToolController::class, 'process'])->name('process');
});

Route::get('/tools/images/image-compressor', [ImageCompressorController::class, 'show'])->name('api.tools.image-compressor.show');
Route::get('/tools/images/image-converter', [ImageConverterController::class, 'show'])->name('api.tools.image-converter.show');
Route::get('/tools/images/image-resizer', [ImageResizerController::class, 'show'])->name('api.tools.image-resizer.show');
Route::get('/tools/images/favicon-generator', [FaviconGeneratorController::class, 'show'])->name('api.tools.favicon-generator.show');

Route::prefix('tools/merge-pdf')->group(function (): void {
    Route::get('/', [MergePdfController::class, 'show']);
    Route::post('/merge', [MergePdfController::class, 'merge']);
});
Route::prefix('tools/{tool}')->whereIn('tool', ['split-pdf', 'pdf-to-jpg', 'jpg-to-pdf', 'remove-pdf-pages', 'compress-pdf', 'rotate-pdf', 'protect-pdf', 'unlock-pdf'])->group(function (): void {
    Route::get('/', [PdfToolController::class, 'show']);
    Route::post('/process', [PdfToolController::class, 'process']);
});
Route::get('/tools/image-compressor', [ImageCompressorController::class, 'show']);
Route::get('/tools/image-converter', [ImageConverterController::class, 'show']);
Route::get('/tools/image-resizer', [ImageResizerController::class, 'show']);
Route::get('/tools/favicon-generator', [FaviconGeneratorController::class, 'show']);

Route::post('/tools/redirect-checker', RedirectCheckerController::class)->name('api.tools.redirect-checker');
Route::post('/tools/http-header-checker', HttpHeaderCheckerController::class)->name('api.tools.http-header-checker');
Route::post('/analytics/tool-events', [ToolEventController::class, 'store'])
    ->middleware('throttle:120,1')
    ->name('api.analytics.tool-events.store');

Route::prefix('tools/short-links')->name('api.tools.short-links.')->group(function (): void {
    Route::get('/', [ShortLinkController::class, 'show'])->name('show');
    Route::post('/', [ShortLinkController::class, 'store'])->name('store');
    Route::get('/{code}', [ShortLinkController::class, 'stats'])->name('stats');
});
