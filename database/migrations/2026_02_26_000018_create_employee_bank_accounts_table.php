<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('account_title')->nullable();
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->timestamps();
            $table->index(['company_id','employee_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('employee_bank_accounts');
    }
};
