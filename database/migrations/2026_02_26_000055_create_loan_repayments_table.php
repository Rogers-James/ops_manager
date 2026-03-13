<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_loan_id')->constrained('employee_loans')->cascadeOnDelete();
            $table->foreignId('payroll_run_id')->nullable()->constrained('payroll_runs')->nullOnDelete();
            $table->decimal('amount',12,2);
            $table->date('paid_on');
            $table->timestamps();
            $table->index(['company_id','employee_loan_id','paid_on'], 'idx_sga_co_emp_from');
        });
    }

    public function down(): void {
        Schema::dropIfExists('loan_repayments');
    }
};
