<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payroll_run_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('payroll_run_id')->constrained('payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->decimal('gross',12,2)->default(0);
            $table->decimal('deductions',12,2)->default(0);
            $table->decimal('net',12,2)->default(0);
            $table->enum('status',['generated','adjusted','finalized'])->default('generated');
            $table->timestamps();
            $table->unique(
                ['company_id','payroll_run_id','employee_id'],
                'uniq_ssi_co_struct_comp'
            );
        });
    }

    public function down(): void {
        Schema::dropIfExists('payroll_run_items');
    }
};
