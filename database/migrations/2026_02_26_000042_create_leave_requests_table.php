<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_days',8,2);
            $table->text('reason')->nullable();
            $table->enum('status',['draft','submitted','approved','rejected','cancelled'])->default('draft');
            $table->foreignId('workflow_id')->nullable()->constrained('workflows')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'employee_id', 'start_date','end_date'], 'idx_sga_co_emp_from');
        });
    }

    public function down(): void {
        Schema::dropIfExists('leave_requests');
    }
};
