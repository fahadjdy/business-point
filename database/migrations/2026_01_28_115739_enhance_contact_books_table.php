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
        Schema::table('contact_books', function (Blueprint $table) {
            $table->string('designation')->nullable()->after('name');
            $table->string('department')->nullable()->after('designation');
            $table->string('email')->nullable()->after('phone');
            $table->text('description')->nullable()->after('address');
            $table->boolean('is_active')->default(true)->after('type');
            $table->integer('sort_order')->default(0)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_books', function (Blueprint $table) {
            $table->dropColumn(['designation', 'department', 'email', 'description', 'is_active', 'sort_order']);
        });
    }
};
