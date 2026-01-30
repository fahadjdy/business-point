<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emergency_contacts', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
            $table->renameColumn('phone', 'contact_number');
            $table->string('icon', 100)->nullable()->after('contact_number');
            $table->string('badge', 50)->nullable()->after('icon');
            $table->string('color', 20)->nullable()->after('badge');
            $table->integer('sort_order')->default(0)->after('color');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('priority');
            $table->integer('sort_order')->default(0)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergency_contacts', function (Blueprint $table) {
            $table->renameColumn('name', 'title');
            $table->renameColumn('contact_number', 'phone');
            $table->dropColumn(['icon', 'badge', 'color', 'sort_order']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'sort_order']);
        });
    }
};
