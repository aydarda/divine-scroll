<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attributions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('passage_id')->constrained()->cascadeOnDelete();
            $t->foreignId('person_id')->constrained('persons')->cascadeOnDelete();
            $t->enum('basis', ['stated_in_text', 'traditional', 'speaker_only', 'unattributed']);
            $t->text('note')->nullable(); // shown on the post's attribution badge
            $t->timestamps();

            $t->unique(['passage_id', 'person_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributions');
    }
};
