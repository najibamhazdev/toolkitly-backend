<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('short_links', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 24)->unique();
            $table->text('destination_url');
            $table->unsignedBigInteger('clicks')->default(0);
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('last_clicked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_links');
    }
};
