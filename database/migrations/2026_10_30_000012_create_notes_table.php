<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Private notes: one per user per passage. Never shown to anyone but the owner.
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('passage_id')->constrained()->cascadeOnDelete();
            $t->text('body');
            $t->timestamps();

            $t->unique(['user_id', 'passage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
