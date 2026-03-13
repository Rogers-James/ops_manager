<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('module');
            $table->string('key');
            $table->string('label');
            $table->enum('type',['text','number','date','select','multiselect','boolean','file'])->default('text');
            $table->boolean('is_required')->default(false);
            $table->json('validation')->nullable();
            $table->timestamps();
            $table->unique(['company_id','module','key']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('custom_fields');
    }
};
