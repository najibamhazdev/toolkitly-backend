<?php

use App\Http\Controllers\Api\MergePdfController;
use App\Http\Controllers\Api\PdfToolController;
use App\Http\Controllers\ShortLinkRedirectController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'tools.qr-code-generator');

Route::view('/qr-code-generator', 'tools.qr-code-generator')->name('tools.qr-code-generator');
Route::view('/barcode-generator', 'tools.barcode-generator')->name('tools.barcode-generator');
Route::view('/short-links', 'tools.short-links')->name('tools.short-links');

Route::prefix('pdf')->group(function (): void {
    Route::view('/merge-pdf', 'tools.merge-pdf')->name('tools.merge-pdf');
    Route::get('/merge-pdf/download/{id}', [MergePdfController::class, 'download'])->name('tools.merge-pdf.download');
    Route::view('/split-pdf', 'tools.split-pdf')->name('tools.split-pdf');
    Route::view('/pdf-to-jpg', 'tools.pdf-to-jpg')->name('tools.pdf-to-jpg');
    Route::view('/jpg-to-pdf', 'tools.jpg-to-pdf')->name('tools.jpg-to-pdf');
    Route::view('/remove-pdf-pages', 'tools.remove-pdf-pages')->name('tools.remove-pdf-pages');
    Route::get('/{tool}/download/{id}/{filename}', [PdfToolController::class, 'download'])->name('tools.pdf.download');
});

Route::prefix('images')->group(function (): void {
    Route::view('/image-compressor', 'tools.image-compressor')->name('tools.image-compressor');
    Route::view('/image-converter', 'tools.image-converter')->name('tools.image-converter');
    Route::view('/image-resizer', 'tools.image-resizer')->name('tools.image-resizer');
    Route::view('/favicon-generator', 'tools.favicon-generator')->name('tools.favicon-generator');
});

Route::redirect('/merge-pdf', '/pdf/merge-pdf', 301);
Route::redirect('/split-pdf', '/pdf/split-pdf', 301);
Route::redirect('/pdf-to-jpg', '/pdf/pdf-to-jpg', 301);
Route::redirect('/jpg-to-pdf', '/pdf/jpg-to-pdf', 301);
Route::redirect('/remove-pdf-pages', '/pdf/remove-pdf-pages', 301);
Route::redirect('/image-compressor', '/images/image-compressor', 301);
Route::redirect('/image-converter', '/images/image-converter', 301);
Route::redirect('/image-resizer', '/images/image-resizer', 301);
Route::redirect('/favicon-generator', '/images/favicon-generator', 301);

Route::get('/s/{code}', ShortLinkRedirectController::class)->name('short-links.redirect');
