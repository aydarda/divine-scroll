<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('person_relations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('person_id')->constrained('persons')->cascadeOnDelete();
            $t->enum('relation', ['father', 'mother', 'wife', 'husband', 'son', 'daughter', 'brother', 'sister', 'other']);
            $t->foreignId('related_person_id')->nullable()->constrained('persons')->nullOnDelete(); // set when the relative is also a voice
            $t->string('related_name');
            $t->enum('basis', ['stated_in_text', 'traditional', 'scholarly_estimate']);
            $t->string('source')->nullable();
            $t->text('note')->nullable();
            $t->unsignedTinyInteger('sort')->default(0);
            $t->timestamps();

            $t->index('person_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_relations');
    }
};
