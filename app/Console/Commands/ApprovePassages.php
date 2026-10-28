<?php

namespace App\Console\Commands;

use App\Models\Passage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Console\Command;

// Place at app/Console/Commands/ApprovePassages.php
//
//   php artisan passages:approve                 Review pending passages one by one
//   php artisan passages:approve --list          Show every passage and its status
//   php artisan passages:approve 1 2 3           Approve passages by ID
//   php artisan passages:approve 4 --reject      Reject by ID
//   php artisan passages:approve 4 --reset       Send back to pending
//   php artisan passages:approve --all           Approve every pending passage that resolves
//   ...add --book=psalms and/or --basis=stated_in_text to --all or --list to narrow them
class ApprovePassages extends Command
{
    protected $signature = 'passages:approve
        {ids?* : Passage IDs to act on}
        {--all : Approve every pending passage that resolves}
        {--reject : Reject the given IDs}
        {--reset : Send the given IDs back to pending}
        {--list : Show every passage and its status}
        {--book= : With --list or --all, limit to one book slug (e.g. psalms)}
        {--basis= : With --list or --all, limit to one basis (stated_in_text, traditional, speaker_only, unattributed)}
        {--translation=WEB}';

    protected $description = 'Review and approve passages before they appear in the feed';

    public function handle(): int
    {
        $t = strtoupper($this->option('translation'));

        if ($this->option('list')) {
            return $this->showList();
        }

        if ($this->argument('ids')) {
            return $this->bulk($this->argument('ids'), $t);
        }

        if ($this->option('all')) {
            return $this->approveAll($t);
        }

        return $this->review($t);
    }

    private function showList(): int
    {
        $rows = $this->filtered(Passage::with(['book', 'attribution'])->orderBy('id'))->get()->map(fn (Passage $p) => [
            $p->id,
            $p->reference,
            $p->attribution?->person->name ?? '-',
            $p->attribution?->basisLabel() ?? '-',
            $p->status,
        ]);

        $this->table(['ID', 'Passage', 'Poster', 'Basis', 'Status'], $rows);

        return self::SUCCESS;
    }

    private function filtered(Builder $query): Builder
    {
        if ($book = $this->option('book')) {
            $query->whereHas('book', fn ($q) => $q->where('slug', $book));
        }

        if ($basis = $this->option('basis')) {
            $query->whereHas('attribution', fn ($q) => $q->where('basis', $basis));
        }

        return $query;
    }

    private function bulk(array $ids, string $t): int
    {
        $status = $this->option('reject') ? 'rejected' : ($this->option('reset') ? 'pending' : 'approved');
        $failed = 0;

        foreach ($ids as $id) {
            $p = Passage::with('book')->find($id);

            if (! $p) {
                $this->warn("#{$id}: not found.");
                $failed++;
                continue;
            }

            if ($status === 'approved' && ! $this->resolves($p, $t)) {
                $this->error("#{$id} {$p->reference}: verse text is missing or empty. Not approved.");
                $failed++;
                continue;
            }

            $this->setStatus($p, $status);
            $this->info("#{$id} {$p->reference}: {$status}.");
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function approveAll(string $t): int
    {
        $pending = $this->filtered(Passage::with('book')->where('status', 'pending')->orderBy('id'))->get();
        $ready = $pending->filter(fn (Passage $p) => $this->resolves($p, $t));

        foreach ($pending->diff($ready) as $p) {
            $this->warn("#{$p->id} {$p->reference} does not resolve and will be skipped.");
        }

        if ($ready->isEmpty()) {
            $this->info('Nothing to approve.');
            return self::SUCCESS;
        }

        if (! $this->confirm("Approve {$ready->count()} passage(s) without reading them first?", false)) {
            return self::SUCCESS;
        }

        $ready->each(fn (Passage $p) => $this->setStatus($p, 'approved'));
        $this->info("{$ready->count()} passage(s) approved.");

        return self::SUCCESS;
    }

    private function review(string $t): int
    {
        $pending = Passage::with(['book', 'attribution'])->where('status', 'pending')->orderBy('id')->get();

        if ($pending->isEmpty()) {
            $this->info('Nothing pending.');
            return self::SUCCESS;
        }

        $count = ['approved' => 0, 'rejected' => 0, 'skipped' => 0];

        foreach ($pending as $i => $p) {
            $a = $p->attribution;

            $this->newLine();
            $this->line(sprintf('<info>[%d/%d] #%d  %s</info>', $i + 1, $pending->count(), $p->id, $p->reference));
            $this->line('Poster:  ' . ($a ? "{$a->person->name} ({$a->person->handle})" : 'none'));
            $this->line('Basis:   ' . ($a ? $a->basisLabel() : '-'));
            if ($a?->note) {
                $this->line('Note:    ' . $a->note);
            }
            $this->line('Themes:  ' . implode(', ', $p->themes ?? []));
            $this->line('Why:     ' . $p->reason);
            $this->newLine();

            $text = $p->verses($t)->pluck('text')->implode(' ');
            $this->line($text !== '' ? $text : '<error>No verse text found.</error>');
            $this->newLine();

            if (! $this->resolves($p, $t)) {
                $this->error('Verse text is missing or empty. Skipping.');
                $count['skipped']++;
                continue;
            }

            $choice = $this->choice('Decision', ['approve', 'reject', 'skip', 'quit'], 'skip');

            if ($choice === 'quit') {
                break;
            }

            if ($choice === 'approve') {
                $this->setStatus($p, 'approved');
                $count['approved']++;
            } elseif ($choice === 'reject') {
                $this->setStatus($p, 'rejected');
                $count['rejected']++;
            } else {
                $count['skipped']++;
            }
        }

        $this->newLine();
        $this->info("Approved {$count['approved']}, rejected {$count['rejected']}, skipped {$count['skipped']}.");

        return self::SUCCESS;
    }

    // A passage can only go live if every verse in its range exists and has text.
    private function resolves(Passage $p, string $t): bool
    {
        $expected = $p->verse_end - $p->verse_start + 1;

        return $p->verses($t)->where('text', '<>', '')->count() === $expected;
    }

    private function setStatus(Passage $p, string $status): void
    {
        $p->update([
            'status'      => $status,
            'approved_at' => $status === 'approved' ? now() : null,
        ]);
    }
}
