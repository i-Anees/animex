<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('overline')->nullable();   // "Drop 014 — Live Now"
            $table->string('title');                   // "Ninja Legacy"
            $table->text('subtitle')->nullable();
            $table->string('image_id')->nullable();    // unsplash photo id
            $table->string('image_url')->nullable();   // direct image url (overrides image_id)
            $table->string('tone', 9)->default('#06121c');
            $table->string('accent', 9)->default('#2BE2FF');
            $table->string('accent2', 9)->default('#1E6BFF');
            $table->string('cta_label')->default('Shop the Drop');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
