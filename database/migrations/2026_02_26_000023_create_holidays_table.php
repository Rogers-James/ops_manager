<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('holiday_calendar_id')->constrained('holiday_calendars')->cascadeOnDelete();
            $table->date('date');
            $table->string('name');
            $table->timestamps();
            $table->unique(['company_id','holiday_calendar_id','date']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('holidays');
    }
};
