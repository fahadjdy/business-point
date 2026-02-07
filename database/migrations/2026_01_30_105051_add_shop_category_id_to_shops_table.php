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
        Schema::table('shops', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_category_id')->nullable()->after('shop_name');
            $table->foreign('shop_category_id')->references('id')->on('shop_categories')->onDelete('set null');
            
            // We will drop 'category' later after data migration manually if needed, 
            // but for now user asked to replace it. 
            // We can drop it now if we assume no critical data, OR keep it temporary.
            // Let's drop it as per instruction "replace".
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeign(['shop_category_id']);
            $table->dropColumn('shop_category_id');
            $table->string('category')->nullable();
        });
    }
};
