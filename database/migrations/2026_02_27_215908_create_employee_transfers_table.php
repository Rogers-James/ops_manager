<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();

            // from (snapshot)
            $table->foreignId('from_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('from_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('from_designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->foreignId('from_grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->foreignId('from_cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->foreignId('from_manager_id')->nullable()->constrained('employees')->nullOnDelete();

            // to
            $table->foreignId('to_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('to_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('to_designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->foreignId('to_grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->foreignId('to_cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->foreignId('to_manager_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->date('effective_date');
            $table->text('reason')->nullable();

            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'cancelled'])->default('submitted');
            $table->timestamps();

            $table->index(['company_id', 'employee_id', 'status'], 'idx_emp_transfers_main');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_transfers');
    }
};
