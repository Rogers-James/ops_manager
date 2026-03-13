<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payslip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('payslip_id')->constrained('payslips')->cascadeOnDelete();
            $table->foreignId('salary_component_id')->constrained('salary_components')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->timestamps();
            $table->index(['company_id', 'payslip_id', 'salary_component_id'], 'idx_sga_co_emp_from');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslip_items');
    }
};
