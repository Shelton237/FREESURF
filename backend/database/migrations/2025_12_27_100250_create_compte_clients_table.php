<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('compte_clients', function (Blueprint $table) {
            $table->id();
            $table->string('telephone')->unique();
            $table->string('email')->nullable();
            $table->string('nom')->nullable();
            $table->string('statut')->default('actif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compte_clients');
    }
};

