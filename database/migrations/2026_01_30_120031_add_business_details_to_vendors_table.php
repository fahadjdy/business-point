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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('business_name', 150)->after('vendor_type');
            $table->text('description')->nullable()->after('business_name');
            $table->string('phone', 20)->nullable()->after('description');
            $table->string('email', 150)->nullable()->after('phone');
            $table->string('website', 255)->nullable()->after('email');
            $table->text('address')->nullable()->after('website');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 100)->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'description', 'phone', 'email', 'website', 'address', 'city', 'state']);
        });
    }
};
