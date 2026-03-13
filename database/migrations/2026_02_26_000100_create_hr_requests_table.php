<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hr_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('hr_request_type_id')->constrained('hr_request_types')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->json('payload')->nullable();
            $table->enum('status',['draft','submitted','approved','rejected','cancelled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id','employee_id','status'], 'idx_sga_co_emp_from');
        });
    }

    public function down(): void {
        Schema::dropIfExists('hr_requests');
    }
};
