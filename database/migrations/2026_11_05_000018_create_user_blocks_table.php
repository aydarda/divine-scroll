<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // One person choosing not to see another person's comments.
    public function up(): void
    {
        Schema::create('user_blocks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('blocked_id')->constrained('users')->cascadeOnDelete();
            $t->timestamps();

            $t->unique(['blocker_id', 'blocked_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_blocks');
    }
};
