<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('performance_cycle_id')->constrained('performance_cycles')->cascadeOnDelete();
            $table->json('kpis');
            $table->timestamps();
            $table->unique(
                ['company_id','employee_id','performance_cycle_id'],
                'uniq_ssi_co_struct_comp'
            );
        });
    }

    public function down(): void {
        Schema::dropIfExists('employee_kpis');
    }
};
