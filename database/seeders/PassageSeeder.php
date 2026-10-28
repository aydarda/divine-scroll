<?php

namespace Database\Seeders;

use App\Models\Attribution;
use App\Models\Book;
use App\Models\Passage;
use App\Models\PassageReference;
use App\Models\Person;
use Illuminate\Database\Seeder;

// References only. Verse text is imported separately from a public-domain translation.
// Every passage starts as "pending" and needs human approval before it appears in the feed.
class PassageSeeder extends Seeder
{
    public function run(): void
    {
        // [book slug, chapter, verse start, verse end, person slug, basis, attribution note, themes, reason]
        $rows = [
            ['psalms', 23, 1, 6, 'david', 'stated_in_text', 'Heading: "A Psalm of David."', ['comfort', 'trust'], 'Core David voice; anchors the feed.'],
            ['psalms', 51, 1, 4, 'david', 'stated_in_text', 'Heading ties it to Nathan confronting David after Bathsheba.', ['repentance'], 'Sets up a later Nathan reply.'],
            ['psalms', 22, 1, 2, 'david', 'stated_in_text', 'Heading: "A Psalm of David."', ['lament'], 'Quoted by Jesus in Matthew 27:46.'],
            ['psalms', 42, 1, 3, 'sons-of-korah', 'stated_in_text', 'Heading names the sons of Korah.', ['longing', 'lament'], 'A psalm voice other than David.'],
            ['psalms', 73, 25, 26, 'asaph', 'stated_in_text', 'Heading: "A Psalm of Asaph."', ['trust', 'doubt'], 'Asaph account; ends a struggle with envy on trust.'],
            ['psalms', 90, 1, 2, 'moses', 'stated_in_text', 'Heading: "A Prayer of Moses, the man of God."', ['eternity'], 'Moses posting in the Psalms.'],
            ['psalms', 121, 1, 4, 'anonymous', 'unattributed', 'The heading, "A Song of Ascents," names no author in the Hebrew text.', ['protection'], 'Tests the Anonymous account.'],
            ['psalms', 127, 1, 2, 'solomon', 'stated_in_text', 'Heading: "Of Solomon." The Hebrew preposition can also mean "for" or "belonging to."', ['work', 'family'], 'Solomon voice inside the Psalms.'],
            ['proverbs', 3, 5, 6, 'solomon', 'stated_in_text', 'Proverbs 1:1 attributes the opening collection to Solomon.', ['wisdom', 'trust'], 'Solomon\'s best-known counsel.'],
            ['proverbs', 31, 8, 9, 'king-lemuel', 'stated_in_text', 'Proverbs 31:1 names King Lemuel. His identity is unknown.', ['justice'], 'A lesser-known voice.'],
            ['ecclesiastes', 3, 1, 4, 'solomon', 'traditional', 'The text names "the Preacher, son of David, king in Jerusalem." Solomon is the traditional identification.', ['time', 'seasons'], 'Shows the traditional badge.'],
            ['song-of-solomon', 8, 6, 7, 'solomon', 'traditional', 'Verse 1:1 says "Solomon\'s," which in Hebrew can mean by, for, or about.', ['love'], 'Shows an ambiguous attribution.'],
            ['isaiah', 40, 28, 31, 'isaiah', 'traditional', 'The book names Isaiah in 1:1. Many scholars date chapters 40 to 55 later.', ['comfort', 'strength'], 'Shows a disputed attribution.'],
            ['jeremiah', 29, 11, 13, 'jeremiah', 'stated_in_text', 'The book opens "The words of Jeremiah." This is a letter to exiles in Babylon.', ['hope', 'exile'], 'Context note guards against misuse.'],
            ['lamentations', 3, 22, 23, 'jeremiah', 'traditional', 'The book is anonymous in the text. Jeremiah is the traditional attribution.', ['hope', 'lament'], 'Anonymous book, traditional voice.'],
            ['zechariah', 9, 9, 9, 'zechariah-prophet', 'traditional', 'The book names Zechariah in 1:1. Chapters 9 to 14 are often debated.', ['king', 'messianic'], 'Quoted in Matthew 21:5.'],
            ['luke', 1, 68, 69, 'zechariah-priest', 'speaker_only', 'Spoken by Zechariah, father of John the Baptist, as recorded by Luke.', ['praise', 'prophecy'], 'Separates the two Zechariahs.'],
            ['matthew', 5, 3, 6, 'jesus', 'speaker_only', 'Jesus speaks; the words are recorded in a Gospel the text does not sign.', ['blessing'], 'Opening of the Sermon on the Mount.'],
            ['matthew', 27, 46, 46, 'jesus', 'speaker_only', 'Jesus speaks; the words are recorded in a Gospel the text does not sign.', ['lament'], 'Quote-post of Psalm 22:1.'],
            ['romans', 8, 38, 39, 'paul', 'stated_in_text', 'Romans 1:1 names Paul.', ['assurance', 'love'], 'Clear named-author example.'],
        ];

        foreach ($rows as [$book, $chapter, $start, $end, $person, $basis, $note, $themes, $reason]) {
            $passage = Passage::firstOrCreate(
                [
                    'book_id'     => Book::where('slug', $book)->firstOrFail()->id,
                    'chapter'     => $chapter,
                    'verse_start' => $start,
                    'verse_end'   => $end,
                ],
                ['themes' => $themes, 'reason' => $reason, 'status' => 'pending'],
            );

            Attribution::firstOrCreate(
                ['passage_id' => $passage->id, 'person_id' => Person::where('slug', $person)->firstOrFail()->id],
                ['basis' => $basis, 'note' => $note],
            );
        }

        $this->seedReferences();
    }

    private function seedReferences(): void
    {
        $find = fn (string $book, int $ch, int $vs) => Passage::where('book_id', Book::where('slug', $book)->value('id'))
            ->where('chapter', $ch)->where('verse_start', $vs)->firstOrFail();

        PassageReference::firstOrCreate(
            [
                'from_passage_id' => $find('matthew', 27, 46)->id,
                'to_passage_id'   => $find('psalms', 22, 1)->id,
                'type'            => 'quote',
            ],
            ['note' => 'Jesus quotes the opening of Psalm 22.'],
        );
    }
}
