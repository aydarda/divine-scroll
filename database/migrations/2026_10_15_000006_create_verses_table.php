<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Imported public-domain text (WEB or KJV). Never filled from AI output.
    public function up(): void
    {
        Schema::create('verses', function (Blueprint $t) {
            $t->id();
            $t->string('translation', 10);
            $t->foreignId('book_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('chapter');
            $t->unsignedSmallInteger('verse');
            $t->text('text');

            $t->unique(['translation', 'book_id', 'chapter', 'verse']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verses');
    }
};
