<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('asset_category_id')->constrained('asset_categories')->restrictOnDelete();
            $table->string('tag');
            $table->string('name');
            $table->string('serial_no')->nullable();
            $table->enum('status',['available','assigned','repair','retired'])->default('available');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id','tag']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('assets');
    }
};
