<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leave_policy_leave_types', function (Blueprint $table) {
                        $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('leave_policy_id')->constrained('leave_policies')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->decimal('annual_quota',8,2)->default(0);
            $table->decimal('monthly_accrual',8,2)->default(0);
            $table->timestamps();
            $table->index(['company_id', 'leave_policy_id', 'leave_type_id'], 'idx_sga_co_emp_from');
        });
    }

    public function down(): void {
        Schema::dropIfExists('leave_policy_leave_types');
    }
};
