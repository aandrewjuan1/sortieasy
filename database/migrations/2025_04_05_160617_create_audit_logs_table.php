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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Tracks who made the change
            $table->string('action'); // Describes the action performed (e.g., 'updated', 'created', 'deleted')
            $table->text('description'); // Detailed description of the change (e.g., "Updated product price from $10 to $12")
            $table->string('table_name'); // The table that was affected (e.g., 'products', 'transactions')
            $table->unsignedBigInteger('record_id'); // The ID of the record that was affected (e.g., product ID, transaction ID)
            $table->timestamps();

            $table->index('created_at'); // For easier querying by date
            $table->index('user_id'); // For tracking actions by users
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
