<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->text('motif_annulation')->nullable()->after('commentaire');
            $table->timestamp('cancelled_at')->nullable()->after('motif_annulation');
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropColumn(['motif_annulation', 'cancelled_at']);
        });
    }
};
