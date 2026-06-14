<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();      // demon-slayer
            $table->string('name');                 // Demon Slayer
            $table->string('tag')->nullable();      // Blade Division
            $table->string('accent', 9)->nullable();// #2BE2A8
            $table->string('tone', 9)->nullable();  // #0c1a1e
            $table->string('image_id')->nullable(); // unsplash photo id
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
