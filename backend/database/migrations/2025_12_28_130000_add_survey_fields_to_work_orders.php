<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('survey_result')->nullable()->after('checklist');
            $table->text('survey_reason')->nullable()->after('survey_result');
            $table->boolean('survey_follow_up')->default(false)->after('survey_reason');
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['survey_result', 'survey_reason', 'survey_follow_up']);
        });
    }
};
