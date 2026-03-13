<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('resignations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->date('resignation_date');
            $table->date('last_working_day');
            $table->text('reason')->nullable();
            $table->enum('status',['submitted','approved','withdrawn','rejected'])->default('submitted');
            $table->timestamps();
            $table->index(['company_id','employee_id','status'], 'idx_sga_co_emp_from');
        });
    }

    public function down(): void {
        Schema::dropIfExists('resignations');
    }
};
