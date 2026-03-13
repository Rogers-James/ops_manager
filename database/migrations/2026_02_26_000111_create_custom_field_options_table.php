<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('custom_field_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('custom_field_id')->constrained('custom_fields')->cascadeOnDelete();
            $table->string('value');
            $table->string('label');
            $table->timestamps();
            $table->unique(
                ['company_id','custom_field_id','value'],
                'uniq_ssi_co_struct_comp'
            );
        });
    }

    public function down(): void {
        Schema::dropIfExists('custom_field_options');
    }
};
