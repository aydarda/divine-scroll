<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // One row per fact about a voice, each with how we know it and where the text says it.
    public function up(): void
    {
        Schema::create('person_facts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('person_id')->constrained('persons')->cascadeOnDelete();
            $t->enum('kind', ['born', 'hometown', 'lived', 'years', 'role']);
            $t->string('value');
            $t->smallInteger('year_from')->nullable(); // negative = BC
            $t->smallInteger('year_to')->nullable();
            $t->enum('basis', ['stated_in_text', 'traditional', 'scholarly_estimate']);
            $t->string('source')->nullable();           // verse reference(s)
            $t->text('note')->nullable();
            $t->unsignedTinyInteger('sort')->default(0);
            $t->timestamps();

            $t->index(['person_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_facts');
    }
};
