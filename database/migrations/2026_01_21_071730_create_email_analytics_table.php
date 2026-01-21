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
        Schema::create('email_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sent_email_id')->constrained('sent_emails')->onDelete('cascade');
            $table->enum('event_type', ['open', 'click', 'unsubscribe', 'complaint', 'bounce']);
            $table->timestamp('event_time');
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('url')->nullable();
            $table->json('event_data')->nullable();
            $table->timestamps();
            
            $table->index(['sent_email_id', 'event_type']);
            $table->index('event_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_analytics');
    }
};
