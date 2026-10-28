<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Psalm headings ("A Psalm by David...") kept apart from verse 1, as the source text has them.
    public function up(): void
    {
        Schema::create('chapter_headings', function (Blueprint $t) {
            $t->id();
            $t->string('translation', 10);
            $t->foreignId('book_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('chapter');
            $t->text('text');

            $t->unique(['translation', 'book_id', 'chapter']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_headings');
    }
};
