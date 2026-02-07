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
        Schema::table('contact_numbers', function (Blueprint $table) {
            $table->enum('type', ['person', 'business', 'service'])->default('person')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_numbers', function (Blueprint $table) {
            $table->enum('type', ['primary', 'secondary', 'whatsapp', 'landline'])->default('primary')->change();
        });
    }
};
