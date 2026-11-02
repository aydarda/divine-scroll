<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // A plain-text copy of each passage (reference, heading, words, voice, themes) that search reads.
    public function up(): void
    {
        Schema::table('passages', function (Blueprint $t) {
            $t->mediumText('search_text')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('passages', function (Blueprint $t) {
            $t->dropColumn('search_text');
        });
    }
};
