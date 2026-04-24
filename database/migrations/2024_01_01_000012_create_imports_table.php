<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('source')->default('csv'); // csv, api
            $table->string('filename')->nullable();
            $table->integer('total')->default(0);
            $table->integer('created')->default(0);
            $table->integer('updated')->default(0);
            $table->integer('skipped')->default(0);
            $table->integer('errors')->default(0);
            $table->json('error_details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
