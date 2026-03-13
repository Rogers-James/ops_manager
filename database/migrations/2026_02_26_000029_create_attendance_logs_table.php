<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('attendance_device_id')->nullable()->constrained('attendance_devices')->nullOnDelete();
            $table->dateTime('log_time');
            $table->enum('source',['manual','csv','device','api'])->default('manual');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['company_id','employee_id','log_time']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('attendance_logs');
    }
};
