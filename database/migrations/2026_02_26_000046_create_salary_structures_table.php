<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('pay_schedule_id')->constrained('pay_schedules')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['company_id','name']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('salary_structures');
    }
};
