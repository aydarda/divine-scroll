<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// Usage: php artisan headings:import storage/app/headings.csv --translation=WEB
// CSV columns: book (1-66), chapter, text
class ImportHeadings extends Command
{
    protected $signature = 'headings:import {file} {--translation=WEB}';
    protected $description = 'Import chapter headings (Psalm titles) from a CSV';

    public function handle(): int
    {
        $file = $this->argument('file');
        $translation = strtoupper($this->option('translation'));

        if (! is_file($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        $books = Book::pluck('id', 'position');
        $h = fopen($file, 'r');
        $header = array_map(fn ($c) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $c))), fgetcsv($h) ?: []);
        $records = [];

        while (($line = fgetcsv($h)) !== false) {
            if (count($line) !== count($header)) {
                continue;
            }
            $row = array_combine($header, $line);
            $bookId = $books[(int) ($row['book'] ?? 0)] ?? null;

            if (! $bookId || ! isset($row['chapter'], $row['text'])) {
                $this->error('Bad row: ' . implode(',', $line));
                return self::FAILURE;
            }

            $records[] = [
                'translation' => $translation,
                'book_id'     => $bookId,
                'chapter'     => (int) $row['chapter'],
                'text'        => trim($row['text']),
            ];
        }
        fclose($h);

        DB::table('chapter_headings')->upsert($records, ['translation', 'book_id', 'chapter'], ['text']);

        $this->info(count($records) . " headings imported as {$translation}.");

        return self::SUCCESS;
    }
}
