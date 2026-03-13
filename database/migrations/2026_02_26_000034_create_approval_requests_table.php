<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained('workflows')->cascadeOnDelete();
            $table->string('request_type');
            $table->unsignedBigInteger('request_id');
            $table->unsignedInteger('current_step')->default(1);
            $table->enum('status',['pending','approved','rejected','cancelled'])->default('pending');
            $table->timestamps();
            $table->index(['company_id','request_type','request_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('approval_requests');
    }
};
