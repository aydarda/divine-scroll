<?php

namespace Database\Seeders;

use App\Models\Attribution;
use App\Models\Book;
use App\Models\Passage;
use App\Models\Person;
use Illuminate\Database\Seeder;

// Run: php artisan db:seed --class=ContentWaveSeeder
// Adds the prophets, Proverbs, Ecclesiastes, Song of Solomon, the New Testament letters (except
// Hebrews) and Revelation as PENDING passages, split at the source text's paragraph breaks.
// Passages that overlap an existing passage are skipped so no verse is posted twice.
class ContentWaveSeeder extends Seeder
{
    public function run(): void
    {
        $new = [
            ['ezekiel', 'Ezekiel', 'Priest and prophet among the exiles in Babylon.'],
            ['daniel', 'Daniel', 'Exile in Babylon and the central figure of the book of Daniel.'],
            ['hosea', 'Hosea', 'Prophet to the northern kingdom of Israel.'],
            ['joel', 'Joel', 'Prophet; the book of Joel is named for him.'],
            ['amos', 'Amos', 'Named in his book as one of the sheep breeders of Tekoa.'],
            ['obadiah', 'Obadiah', 'Prophet; the shortest book of the Old Testament is named for him.'],
            ['jonah', 'Jonah', 'Prophet sent to Nineveh; the book of Jonah is about him.'],
            ['micah', 'Micah', 'Prophet from Moresheth.'],
            ['nahum', 'Nahum', 'Prophet whose book announces the fall of Nineveh.'],
            ['habakkuk', 'Habakkuk', 'Prophet who questions God about injustice.'],
            ['zephaniah', 'Zephaniah', 'Prophet in the days of King Josiah.'],
            ['haggai', 'Haggai', 'Prophet who urged the rebuilding of the temple after the exile.'],
            ['malachi', 'Malachi', 'Named in the opening of the last book of the Old Testament.'],
            ['agur', 'Agur', 'Named once, in Proverbs 30:1. Little else is known.'],
            ['james', 'James', 'Named in the opening of the letter of James.'],
            ['peter', 'Peter', 'Apostle; named in the opening of 1 and 2 Peter.'],
            ['john', 'John', 'Names himself in Revelation; by tradition also the author of three letters.'],
            ['jude', 'Jude', 'Names himself in the opening of his letter, as brother of James.'],
        ];

        foreach ($new as [$slug, $name, $bio]) {
            Person::firstOrCreate(['slug' => $slug], [
                'name'   => $name,
                'handle' => '@' . str_replace('-', '', $slug),
                'kind'   => 'author',
                'bio'    => $bio,
            ]);
        }

        $books = Book::pluck('id', 'slug');
        $people = Person::pluck('id', 'slug');
        $created = $skipped = 0;

        $h = fopen(__DIR__ . '/data/wave2.csv', 'r');
        fgetcsv($h); // header

        while (($row = fgetcsv($h)) !== false) {
            [$slug, $chapter, $start, $end, $person, $basis, $note] = $row;
            $bookId = $books[$slug];

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
                'book_id'     => $bookId,
                'chapter'     => (int) $chapter,
                'verse_start' => (int) $start,
                'verse_end'   => (int) $end,
                'status'      => 'pending',
                'reason'      => 'Book-level attribution; split at the source text\'s paragraph breaks.',
            ]);

            Attribution::create([
                'passage_id' => $passage->id,
                'person_id'  => $people[$person],
                'basis'      => $basis,
                'note'       => $note !== '' ? $note : null,
            ]);

            $created++;
        }

        fclose($h);
        $this->command->info("{$created} passages created (pending), {$skipped} skipped as overlaps.");
    }
}
