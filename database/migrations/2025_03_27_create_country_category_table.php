<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('country_category', function (Blueprint $table) {
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->primary(['country_id', 'category_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('country_category');
    }
};

