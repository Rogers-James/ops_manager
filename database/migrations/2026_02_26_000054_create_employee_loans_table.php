<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('amount',12,2);
            $table->date('start_date');
            $table->decimal('installment_amount',12,2);
            $table->enum('status',['active','closed'])->default('active');
            $table->timestamps();
            $table->index(['company_id','employee_id','status'], 'idx_sga_co_emp_from');
        });
    }

    public function down(): void {
        Schema::dropIfExists('employee_loans');
    }
};
