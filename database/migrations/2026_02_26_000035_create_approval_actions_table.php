<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('approval_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('approval_request_id')->constrained('approval_requests')->cascadeOnDelete();
            $table->unsignedInteger('step_order');
            $table->foreignId('acted_by')->constrained('users')->cascadeOnDelete();
            $table->enum('action',['approved','rejected','returned']);
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->index(['company_id','approval_request_id','step_order']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('approval_actions');
    }
};
