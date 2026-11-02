<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\PersonFact;
use App\Models\PersonRelation;
use Illuminate\Database\Seeder;

// Run: php artisan db:seed --class=PersonProfileSeeder
// Loads the voice profiles from two CSV files. Every fact names the verse it comes from.
// Safe to re-run: it replaces the profile rows each time, so edit the CSVs and run it again.
class PersonProfileSeeder extends Seeder
{
    public function run(): void
    {
        $people = Person::pluck('id', 'slug');

        PersonFact::query()->delete();
        PersonRelation::query()->delete();

        $facts = $this->rows('person_facts.csv');
        $order = [];

        foreach ($facts as $r) {
            if (! isset($people[$r['person']])) {
                $this->command->warn("Unknown voice in person_facts.csv: {$r['person']}");
                continue;
            }

            PersonFact::create([
                'person_id' => $people[$r['person']],
                'kind'      => $r['kind'],
                'value'     => $r['value'],
                'year_from' => $r['year_from'] !== '' ? (int) $r['year_from'] : null,
                'year_to'   => $r['year_to'] !== '' ? (int) $r['year_to'] : null,
                'basis'     => $r['basis'],
                'source'    => $r['source'] ?: null,
                'note'      => $r['note'] ?: null,
                'sort'      => $order[$r['person']] = ($order[$r['person']] ?? 0) + 1,
            ]);
        }

        $relations = $this->rows('person_relations.csv');
        $order = [];

        foreach ($relations as $r) {
            if (! isset($people[$r['person']])) {
                $this->command->warn("Unknown voice in person_relations.csv: {$r['person']}");
                continue;
            }

            PersonRelation::create([
                'person_id'         => $people[$r['person']],
                'relation'          => $r['relation'],
                'related_person_id' => $r['related_slug'] !== '' ? ($people[$r['related_slug']] ?? null) : null,
                'related_name'      => $r['related_name'],
                'basis'             => $r['basis'],
                'source'            => $r['source'] ?: null,
                'note'              => $r['note'] ?: null,
                'sort'              => $order[$r['person']] = ($order[$r['person']] ?? 0) + 1,
            ]);
        }

        $this->command->info(count($facts) . ' facts and ' . count($relations) . ' relations loaded.');
    }

    private function rows(string $file): array
    {
        $h = fopen(__DIR__ . '/data/' . $file, 'r');
        $header = fgetcsv($h);
        $rows = [];

        while (($line = fgetcsv($h)) !== false) {
            if (count($line) === count($header)) {
                $rows[] = array_combine($header, $line);
            }
        }

        fclose($h);

        return $rows;
    }
}
