<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_events', function (Blueprint $table): void {
            $table->id();
            $table->string('tool', 100)->index();
            $table->string('action', 60)->index();
            $table->string('status', 30)->default('success')->index();
            $table->json('metadata')->nullable();
            $table->string('visitor_hash', 64)->nullable()->index();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_events');
    }
};
