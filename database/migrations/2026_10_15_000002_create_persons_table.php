<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('persons', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('name');
            $t->string('handle')->unique();
            // author = wrote it, speaker = recorded speech of someone who wrote nothing, anonymous = no named voice
            $t->enum('kind', ['author', 'speaker', 'anonymous']);
            $t->text('bio')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
