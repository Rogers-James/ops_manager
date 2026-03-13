<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->enum('type',['earning','deduction']);
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_statutory')->default(false);
            $table->enum('calculation_type',['fixed','formula','percentage'])->default('fixed');
            $table->text('formula')->nullable();
            $table->timestamps();
            $table->unique(['company_id','code']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('salary_components');
    }
};
