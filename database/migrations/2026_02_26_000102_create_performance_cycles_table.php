<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('performance_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status',['draft','active','closed'])->default('draft');
            $table->timestamps();
            $table->index(['company_id','status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('performance_cycles');
    }
};
