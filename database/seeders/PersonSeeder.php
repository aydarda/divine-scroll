<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    public function run(): void
    {
        $people = [
            ['david', 'David', 'author', 'King of Israel; the Psalms name him in their headings more than anyone else.'],
            ['solomon', 'Solomon', 'author', 'Son of David and king of Israel; named in Proverbs, the Song of Songs, and two psalms.'],
            ['asaph', 'Asaph', 'author', 'Temple musician; twelve psalms carry his name.'],
            ['sons-of-korah', 'Sons of Korah', 'author', 'A guild of temple singers; eleven psalms are headed with their name.'],
            ['moses', 'Moses', 'author', 'Named in the heading of Psalm 90.'],
            ['king-lemuel', 'King Lemuel', 'author', 'Named once, in Proverbs 31:1. His identity is unknown.'],
            ['isaiah', 'Isaiah', 'author', 'Prophet in Jerusalem; the book of Isaiah is named for him.'],
            ['jeremiah', 'Jeremiah', 'author', 'Prophet before and during the fall of Jerusalem.'],
            ['zechariah-prophet', 'Zechariah (prophet)', 'author', 'Post-exile prophet; not the Zechariah of Luke 1.'],
            ['zechariah-priest', 'Zechariah (priest)', 'speaker', 'Father of John the Baptist; spoken words recorded in Luke 1.'],
            ['jesus', 'Jesus', 'speaker', 'Wrote nothing; his words are recorded by the Gospel writers.'],
            ['paul', 'Paul', 'author', 'Apostle; named as author in the opening of Romans.'],
            ['anonymous', 'Anonymous', 'anonymous', 'Passages whose text names no author.'],
        ];

        foreach ($people as [$slug, $name, $kind, $bio]) {
            Person::updateOrCreate(['slug' => $slug], [
                'name'   => $name,
                'handle' => '@' . str_replace('-', '', $slug),
                'kind'   => $kind,
                'bio'    => $bio,
            ]);
        }
    }
}
