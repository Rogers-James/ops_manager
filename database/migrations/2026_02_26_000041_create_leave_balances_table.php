<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->decimal('opening',8,2)->default(0);
            $table->decimal('accrued',8,2)->default(0);
            $table->decimal('used',8,2)->default(0);
            $table->decimal('adjusted',8,2)->default(0);
            $table->decimal('closing',8,2)->default(0);
            $table->timestamps();
            $table->unique(['company_id','employee_id','leave_type_id','year']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('leave_balances');
    }
};
