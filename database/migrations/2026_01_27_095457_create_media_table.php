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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('model_type', 100);
            $table->unsignedBigInteger('model_id');
            $table->string('file_path', 255);
            $table->string('file_name', 150);
            $table->string('mime_type', 100);
            $table->bigInteger('file_size');
            $table->boolean('is_primary')->default(false);
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->string('delete_reason', 255)->nullable();

            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
