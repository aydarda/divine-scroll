<?php

namespace App\Console\Commands;

use App\Models\Passage;
use Illuminate\Console\Command;

class VerifyPassages extends Command
{
    protected $signature = 'passages:verify {--translation=WEB}';
    protected $description = 'Check that every proposed passage resolves to real verses in the imported text';

    public function handle(): int
    {
        $translation = strtoupper($this->option('translation'));
        $rows = [];
        $bad = 0;

        Passage::with('book')->orderBy('id')->each(function (Passage $p) use ($translation, &$rows, &$bad) {
            $expected = $p->verse_end - $p->verse_start + 1;
            $found = $p->verses($translation)->count();
            $ok = $found === $expected;
            $bad += $ok ? 0 : 1;
            $rows[] = [$p->reference, $p->status, $expected, $found, $ok ? 'ok' : 'MISSING'];
        });

        $this->table(['Passage', 'Status', 'Expected', 'Found', 'Check'], $rows);

        if ($bad) {
            $this->error("{$bad} passage(s) do not resolve. Fix the reference or import the text before approving.");
        }

        return $bad ? self::FAILURE : self::SUCCESS;
    }
}
