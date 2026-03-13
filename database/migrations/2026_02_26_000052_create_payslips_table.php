<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('payroll_run_item_id')->constrained('payroll_run_items')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->date('issue_date');
            $table->timestamps();
            $table->unique(['company_id','payroll_run_item_id']);
            $table->unique(
                ['company_id','payroll_run_item_id'],
                'uniq_ssi_co_struct_comp'
            );
        });
    }

    public function down(): void {
        Schema::dropIfExists('payslips');
    }
};
