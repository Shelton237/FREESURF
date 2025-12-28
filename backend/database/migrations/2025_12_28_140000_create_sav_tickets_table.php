<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sav_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('incident'); // incident|assistance|reclamation
            $table->string('channel')->default('phone'); // phone|whatsapp|portal|email
            $table->string('priority')->default('normal'); // low|normal|high
            $table->string('status')->default('open'); // open|in_progress|pending_client|resolved|closed
            $table->string('subject');
            $table->text('description');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('follow_up_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sav_tickets');
    }
};
