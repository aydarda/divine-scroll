<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// Place at app/Console/Commands/ImportVerses.php
// Usage: php artisan verses:import storage/app/web.csv --translation=WEB
// CSV needs a header row with: book,chapter,verse,text  (JSON: an array of objects with the same keys)
class ImportVerses extends Command
{
    protected $signature = 'verses:import {file : Path to a CSV or JSON file} {--translation=WEB}';
    protected $description = 'Import a public-domain Bible translation into the verses table';

    private const ALIASES = [
        'psalm'            => 'psalms',
        'songofsongs'      => 'songofsolomon',
        'canticles'        => 'songofsolomon',
        'revelationofjohn' => 'revelation',
    ];

    public function handle(): int
    {
        $file = $this->argument('file');
        $translation = strtoupper($this->option('translation'));

        if (! is_file($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        try {
            $rows = $this->readRows($file);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $map = $this->bookMap();
        $records = [];
        $unknown = [];

        foreach ($rows as $row) {
            $key = $this->norm((string) $row['book']);
            $key = self::ALIASES[$key] ?? $key;
            $bookId = $map[$key] ?? null;

            if ($bookId === null) {
                $unknown[$row['book']] = true;
                continue;
            }

            $text = trim(preg_replace('/\s+/u', ' ', str_replace('¶', '', (string) $row['text'])));

            $records[] = [
                'translation' => $translation,
                'book_id'     => $bookId,
                'chapter'     => (int) $row['chapter'],
                'verse'       => (int) $row['verse'],
                'text'        => $text,
            ];
        }

        if ($unknown) {
            $this->error('Unrecognised book names, nothing was imported: ' . implode(', ', array_keys($unknown)));
            return self::FAILURE;
        }

        DB::transaction(function () use ($records) {
            foreach (array_chunk($records, 500) as $chunk) {
                DB::table('verses')->upsert(
                    $chunk,
                    ['translation', 'book_id', 'chapter', 'verse'],
                    ['text'],
                );
            }
        });

        $this->info(count($records) . " verses imported as {$translation}.");
        $this->line('Next: php artisan passages:verify --translation=' . $translation);

        return self::SUCCESS;
    }

    private function readRows(string $file): array
    {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $required = ['book', 'chapter', 'verse', 'text'];

        if ($ext === 'json') {
            $data = json_decode(file_get_contents($file), true);
            if (! is_array($data)) {
                throw new RuntimeException('JSON must be an array of objects with book, chapter, verse, text.');
            }
            $rows = $data;
        } elseif ($ext === 'csv') {
            $h = fopen($file, 'r');
            $header = fgetcsv($h);
            if (! $header) {
                throw new RuntimeException('CSV is empty.');
            }
            $header = array_map(fn ($c) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $c))), $header);
            $rows = [];
            while (($line = fgetcsv($h)) !== false) {
                if (count($line) === count($header)) {
                    $rows[] = array_combine($header, $line);
                }
            }
            fclose($h);
        } else {
            throw new RuntimeException('Only .csv and .json files are supported.');
        }

        foreach ($required as $col) {
            if ($rows && ! array_key_exists($col, $rows[0])) {
                throw new RuntimeException("Missing column: {$col}");
            }
        }

        return $rows;
    }

    private function bookMap(): array
    {
        $map = [];
        foreach (Book::all() as $book) {
            $map[$this->norm($book->name)] = $book->id;
            $map[$this->norm($book->slug)] = $book->id;
            $map[(string) $book->position] = $book->id;
        }

        return $map;
    }

    private function norm(string $s): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($s));
    }
}
