<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Named passage_references because "references" is a reserved word in SQL.
    public function up(): void
    {
        Schema::create('passage_references', function (Blueprint $t) {
            $t->id();
            $t->foreignId('from_passage_id')->constrained('passages')->cascadeOnDelete();
            $t->foreignId('to_passage_id')->constrained('passages')->cascadeOnDelete();
            $t->enum('type', ['quote', 'reply', 'echo']);
            $t->text('note')->nullable();
            $t->timestamps();

            $t->unique(['from_passage_id', 'to_passage_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passage_references');
    }
};
