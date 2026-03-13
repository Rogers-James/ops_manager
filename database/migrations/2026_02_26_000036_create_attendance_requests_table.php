<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date');
            $table->dateTime('requested_first_in')->nullable();
            $table->dateTime('requested_last_out')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status',['draft','submitted','approved','rejected','cancelled'])->default('draft');
            $table->foreignId('workflow_id')->nullable()->constrained('workflows')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id','employee_id','date']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('attendance_requests');
    }
};
