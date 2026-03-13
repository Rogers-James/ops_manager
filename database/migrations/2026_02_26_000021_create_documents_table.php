<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->nullOnDelete();
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');
            $table->string('title')->nullable();
            $table->string('file_path');
            $table->date('expires_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['company_id']);
            $table->index(['owner_type','owner_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('documents');
    }
};
