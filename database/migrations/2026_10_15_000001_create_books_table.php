<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $t) {
            $t->id();
            $t->unsignedTinyInteger('position')->unique();
            $t->string('name');
            $t->string('slug')->unique();
            $t->enum('testament', ['old', 'new']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
