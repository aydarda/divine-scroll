<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Reader suggestions: a new passage to add, or a correction to a passage's attribution.
    public function up(): void
    {
        Schema::create('suggestions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('type', ['passage', 'attribution']);

            // Attribution corrections point at an existing passage.
            $t->foreignId('passage_id')->nullable()->constrained()->cascadeOnDelete();

            // New-passage suggestions give a reference.
            $t->foreignId('book_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedSmallInteger('chapter')->nullable();
            $t->unsignedSmallInteger('verse_start')->nullable();
            $t->unsignedSmallInteger('verse_end')->nullable();

            $t->foreignId('suggested_person_id')->nullable()->constrained('persons')->nullOnDelete();
            $t->string('person_other', 80)->nullable();
            $t->enum('suggested_basis', ['stated_in_text', 'traditional', 'speaker_only', 'unattributed'])->nullable();
            $t->string('source', 200)->nullable();
            $t->text('reason');

            $t->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $t->text('admin_note')->nullable();
            $t->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('moderated_at')->nullable();
            $t->timestamps();

            $t->index(['status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
