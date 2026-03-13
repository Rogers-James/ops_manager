<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('final_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('resignation_id')->constrained('resignations')->cascadeOnDelete();
            $table->foreignId('payroll_run_id')->nullable()->constrained('payroll_runs')->nullOnDelete();
            $table->decimal('amount',12,2)->default(0);
            $table->enum('status',['draft','approved','paid'])->default('draft');
            $table->timestamps();
            $table->unique(['company_id','resignation_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('final_settlements');
    }
};
