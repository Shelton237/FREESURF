<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lien_compte_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compte_client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('statut')->default('actif');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_confirmed_at')->nullable();
            $table->timestamps();

            $table->unique(['compte_client_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lien_compte_clients');
    }
};

