<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Words-only text for passages where the verses mix narration with speech (Jesus' words).
    public function up(): void
    {
        Schema::table('passages', function (Blueprint $t) {
            $t->text('text_override')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('passages', function (Blueprint $t) {
            $t->dropColumn('text_override');
        });
    }
};
