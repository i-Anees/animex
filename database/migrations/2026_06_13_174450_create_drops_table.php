<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drops', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();   // DS-016
            $table->string('name');             // Nichirin Season
            $table->foreignId('collection_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['upcoming', 'active', 'soldout'])->default('upcoming');
            $table->unsignedInteger('sold')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->timestamp('live_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drops');
    }
};
