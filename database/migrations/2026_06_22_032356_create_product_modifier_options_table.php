<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_modifier_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_modifier_id')->constrained()->onDelete('cascade');
            $table->string('name'); // 0%, 50%, No Ice
            $table->decimal('extra_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_modifier_options');
    }
};
