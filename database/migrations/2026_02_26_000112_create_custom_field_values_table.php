<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('custom_field_id')->constrained('custom_fields')->cascadeOnDelete();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->longText('value')->nullable();
            $table->timestamps();
            $table->index(['company_id','entity_type','entity_id'], 'idx_sga_co_emp_from');
        });
    }

    public function down(): void {
        Schema::dropIfExists('custom_field_values');
    }
};
