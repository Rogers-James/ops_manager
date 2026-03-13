<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('rank')->default(0);
            $table->timestamps();
            $table->unique(['company_id','name']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('grades');
    }
};
