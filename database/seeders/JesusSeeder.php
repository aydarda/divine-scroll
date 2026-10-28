<?php

namespace Database\Seeders;

use App\Models\Attribution;
use App\Models\Book;
use App\Models\Passage;
use App\Models\Person;
use Illuminate\Database\Seeder;

// Run: php artisan db:seed --class=JesusSeeder
// Creates PENDING passages holding only the words of Jesus, taken from the source text's own markup.
// Rows with mode "override" add words-only text to passages you already have; passages that
// overlap an existing passage are skipped so no verse is posted twice.
class JesusSeeder extends Seeder
{
    public function run(): void
    {
        $jesus = Person::where('slug', 'jesus')->firstOrFail();
        $books = Book::pluck('id', 'slug');
        $created = $updated = $skipped = 0;

        $h = fopen(__DIR__ . '/data/jesus.csv', 'r');
        fgetcsv($h); // header

        while (($row = fgetcsv($h)) !== false) {
            [$mode, $slug, $chapter, $start, $end, $text, $note] = $row;
            $bookId = $books[$slug];

            if ($mode === 'override') {
                $exact = Passage::where([
                    'book_id' => $bookId, 'chapter' => $chapter, 'verse_start' => $start, 'verse_end' => $end,
                ])->first();

                if ($exact && ! $exact->text_override) {
                    $exact->update(['text_override' => $text]);
                    $updated++;
                }
                continue;
            }

            $overlaps = Passage::where('book_id', $bookId)
                ->where('chapter', $chapter)
                ->where('verse_start', '<=', $end)
                ->where('verse_end', '>=', $start)
                ->exists();

            if ($overlaps) {
                $skipped++;
                continue;
            }

            $passage = Passage::create([
                'book_id'       => $bookId,
                'chapter'       => (int) $chapter,
                'verse_start'   => (int) $start,
                'verse_end'     => (int) $end,
                'text_override' => $text,
                'status'        => 'pending',
                'reason'        => 'Words of Jesus marked in the source text.',
            ]);

            Attribution::create([
                'passage_id' => $passage->id,
                'person_id'  => $jesus->id,
                'basis'      => 'speaker_only',
                'note'       => $note,
            ]);

            $created++;
        }

        fclose($h);
        $this->command->info("{$created} created (pending), {$updated} existing passages updated, {$skipped} skipped as overlaps.");
    }
}
