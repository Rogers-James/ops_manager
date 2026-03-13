<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('pay_schedule_id')->constrained('pay_schedules')->restrictOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status',['draft','processing','approved','locked'])->default('draft');
            $table->foreignId('workflow_id')->nullable()->constrained('workflows')->nullOnDelete();
            $table->timestamps();
            $table->unique(['company_id','pay_schedule_id','period_start','period_end'], 'uniq_payroll_period');
        });
    }

    public function down(): void {
        Schema::dropIfExists('payroll_runs');
    }
};
