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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id')->index();
            $table->enum('actor_type', ['user', 'admin', 'system']);
            $table->bigInteger('actor_id')->nullable();
            $table->string('module', 100);
            $table->string('entity_type', 150);
            $table->bigInteger('entity_id')->nullable();
            $table->enum('action', [
                'create',
                'update',
                'delete',
                'restore',
                'verify',
                'approve',
                'reject',
                'view',
                'search',
                'login',
                'logout',
                'bulk_update',
                'bulk_delete'
            ]);
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
