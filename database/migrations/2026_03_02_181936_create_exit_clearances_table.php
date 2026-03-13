<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exit_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('resignation_id')->constrained('resignations')->cascadeOnDelete();
            $table->date('initiated_on')->nullable();
            $table->enum('status', ['open', 'in_progress', 'cleared', 'hold'])->default('open');
            $table->timestamps();

            $table->unique(['company_id', 'resignation_id'], 'uniq_exit_clearance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exit_clearances');
    }
};
