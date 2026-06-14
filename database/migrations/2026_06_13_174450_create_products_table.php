<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();          // AX-DS01
            $table->string('slug')->unique();         // ax-001
            $table->string('title');                  // Shadow Clan Hoodie
            $table->foreignId('collection_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('drop_label')->nullable(); // DROP 001
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->unsignedInteger('edition')->nullable();   // limited run size
            $table->decimal('rating', 3, 1)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_best')->default(false);
            $table->boolean('is_limited')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('stock')->default(0);
            $table->json('sizes')->nullable();        // ["S","M","L","XL","XXL"]
            $table->json('sold_out_sizes')->nullable();
            $table->json('colors')->nullable();       // ["#000000","#F5F5F5"]
            $table->json('gallery')->nullable();      // [url, url, ...]
            $table->string('image')->nullable();
            $table->string('image_hover')->nullable();
            $table->string('blurb_short')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('collection_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
