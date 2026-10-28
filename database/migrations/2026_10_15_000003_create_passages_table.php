<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('passages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('book_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('chapter');
            $t->unsignedSmallInteger('verse_start');
            $t->unsignedSmallInteger('verse_end');
            $t->json('themes')->nullable();
            $t->text('reason')->nullable(); // why this passage was proposed
            $t->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $t->timestamp('approved_at')->nullable();
            $t->timestamps();

            $t->unique(['book_id', 'chapter', 'verse_start', 'verse_end']);
            $t->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passages');
    }
};
