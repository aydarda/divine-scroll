<?php

namespace Database\Seeders;

use App\Models\Attribution;
use App\Models\Book;
use App\Models\Passage;
use App\Models\Person;
use Illuminate\Database\Seeder;

// Run: php artisan db:seed --class=PsalmSeeder
// Creates the remaining Psalms as PENDING passages, attributed from each psalm's own heading.
// Psalms that already have curated passages (22, 23, 42, 51, 73, 90, 121, 127) are skipped.
class PsalmSeeder extends Seeder
{
    public function run(): void
    {
        Person::updateOrCreate(['slug' => 'ethan'], [
            'name'   => 'Ethan the Ezrahite',
            'handle' => '@ethan',
            'kind'   => 'author',
            'bio'    => 'Named in the heading of Psalm 89.',
        ]);

        $book = Book::where('slug', 'psalms')->firstOrFail();
        $people = Person::pluck('id', 'slug');
        $made = 0;

        $h = fopen(__DIR__ . '/data/psalms.csv', 'r');
        fgetcsv($h); // header

        while (($row = fgetcsv($h)) !== false) {
            [$chapter, $start, $end, $person, $basis, $note] = $row;

            $passage = Passage::firstOrCreate(
                [
                    'book_id'     => $book->id,
                    'chapter'     => (int) $chapter,
                    'verse_start' => (int) $start,
                    'verse_end'   => (int) $end,
                ],
                ['status' => 'pending', 'reason' => 'Imported from the psalm heading; break points chosen automatically.'],
            );

            $made += $passage->wasRecentlyCreated ? 1 : 0;

            Attribution::firstOrCreate(
                ['passage_id' => $passage->id, 'person_id' => $people[$person]],
                ['basis' => $basis, 'note' => $note],
            );
        }

        fclose($h);
        $this->command->info("{$made} passages created (pending).");
    }
}
