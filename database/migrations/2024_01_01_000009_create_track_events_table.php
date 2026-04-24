<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('track_events', function (Blueprint $table) {
            $table->id();
            $table->string('token')->index();
            $table->nullableMorphs('trackable'); // proposal, email, etc.
            $table->string('event')->default('open'); // open, view, click
            $table->string('context')->nullable(); // email_open, proposal_view, etc.
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('track_events');
    }
};
