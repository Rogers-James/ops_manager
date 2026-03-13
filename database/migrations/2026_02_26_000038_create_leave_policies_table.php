<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leave_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->enum('accrual_method',['monthly','yearly','none'])->default('yearly');
            $table->boolean('carry_forward_allowed')->default(false);
            $table->unsignedInteger('max_carry_forward')->nullable();
            $table->boolean('encashment_allowed')->default(false);
            $table->boolean('count_weekends')->default(false);
            $table->boolean('count_holidays')->default(false);
            $table->timestamps();
            $table->unique(['company_id','name']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('leave_policies');
    }
};
