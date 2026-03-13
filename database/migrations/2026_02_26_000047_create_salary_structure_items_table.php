<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary_structure_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('salary_structure_id')->constrained('salary_structures')->cascadeOnDelete();
            $table->foreignId('salary_component_id')->constrained('salary_components')->restrictOnDelete();
            $table->decimal('amount', 12, 2)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(
                ['company_id', 'salary_structure_id', 'salary_component_id'],
                'uniq_ssi_co_struct_comp'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_structure_items');
    }
};
