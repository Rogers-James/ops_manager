<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('legal_name')->nullable()->after('name');
            $table->string('website')->nullable()->after('legal_name');
            $table->string('email')->nullable()->after('website');
            $table->string('phone')->nullable()->after('email');

            // HQ address
            $table->text('hq_address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('hq_address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('postal_code');

            // compliance (optional)
            $table->string('registration_no')->nullable()->after('country');
            $table->string('tax_id')->nullable()->after('registration_no');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'legal_name',
                'website',
                'email',
                'phone',
                'hq_address',
                'city',
                'state',
                'postal_code',
                'country',
                'registration_no',
                'tax_id'
            ]);
        });
    }
};
