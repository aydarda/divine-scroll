<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Passage;
use App\Models\PassageReference;
use Illuminate\Console\Command;

// Usage: php artisan references:import database/seeders/data/xrefs.csv
// CSV columns: from_book, from_chapter, from_verse, to_book, to_chapter, to_verse_start, to_verse_end
// Books are numbered 1 to 66. Each row is a cross-reference note from the source text.
// A link is made for every pair of existing passages that contain the source verse and the target range.
class ImportReferences extends Command
{
    protected $signature = 'references:import {file}';
    protected $description = 'Link passages using the cross-reference notes from the source text';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (! is_file($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        $books = Book::pluck('id', 'position');
        $passages = Passage::select('id', 'book_id', 'chapter', 'verse_start', 'verse_end')
            ->get()
            ->groupBy(fn ($p) => "{$p->book_id}:{$p->chapter}");

        $h = fopen($file, 'r');
        fgetcsv($h); // header
        $rows = $created = $existing = 0;

        while (($r = fgetcsv($h)) !== false) {
            if (count($r) < 7) {
                continue;
            }
            $rows++;
            [$fb, $fc, $fv, $tb, $tc, $ts, $te] = array_map('intval', $r);

            $from = ($passages["{$books[$fb]}:{$fc}"] ?? collect())
                ->filter(fn ($p) => $p->verse_start <= $fv && $p->verse_end >= $fv);
            $to = ($passages["{$books[$tb]}:{$tc}"] ?? collect())
                ->filter(fn ($p) => $p->verse_start <= $te && $p->verse_end >= $ts);

            foreach ($from as $f) {
                foreach ($to as $t) {
                    if ($f->id === $t->id) {
                        continue;
                    }

                    // Never add a second link between the same two passages (keeps hand-made quotes as they are).
                    if (PassageReference::where('from_passage_id', $f->id)->where('to_passage_id', $t->id)->exists()) {
                        $existing++;
                        continue;
                    }

                    PassageReference::create([
                        'from_passage_id' => $f->id,
                        'to_passage_id'   => $t->id,
                        'type'            => 'echo',
                        'note'            => 'Cross-reference in the source text.',
                    ]);
                    $created++;
                }
            }
        }

        fclose($h);
        $this->info("{$rows} cross-reference notes read; {$created} links created, {$existing} already linked.");

        return self::SUCCESS;
    }
}
