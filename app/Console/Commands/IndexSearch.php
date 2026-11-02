<?php

namespace App\Console\Commands;

use App\Models\Passage;
use App\Support\Search;
use Illuminate\Console\Command;

// php artisan search:index          index passages that have no searchable text yet
// php artisan search:index --all    rebuild everything (after changing voices, headings, or wording)
class IndexSearch extends Command
{
    protected $signature = 'search:index {--all : Rebuild every approved passage}';
    protected $description = 'Build the searchable text for passages';

    public function handle(): int
    {
        $query = Passage::approved()->with(['book', 'attribution.person']);

        if (! $this->option('all')) {
            $query->whereNull('search_text');
        }

        $n = 0;

        $query->chunkById(200, function ($passages) use (&$n) {
            foreach ($passages as $p) {
                Search::index($p);
                $n++;
            }
        });

        $this->info("{$n} passage(s) indexed.");

        return self::SUCCESS;
    }
}
