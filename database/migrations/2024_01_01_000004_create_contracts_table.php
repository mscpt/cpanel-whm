<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('renewal_date')->nullable()->index();
            $table->decimal('value', 10, 2)->default(0);
            $table->string('billing_cycle')->default('monthly');
            $table->string('status')->default('active'); // active, suspended, cancelled
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
