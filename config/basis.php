<?php

return [
    [
        'key'     => 'stated_in_text',
        'label'   => 'Author',
        'meaning' => 'The Bible text itself names who is speaking or writing, in the passage or in its heading.',
        'example' => 'Psalm 3 is headed “A Psalm by David.”',
    ],
    [
        'key'     => 'traditional',
        'label'   => 'Traditional attribution',
        'meaning' => 'The credit comes from long-standing tradition. The text may name a person, but scholars debate whether that person wrote this part, or the text doesn’t settle it.',
        'example' => 'The book of Isaiah names Isaiah, though many scholars date chapters 40 to 55 later.',
    ],
    [
        'key'     => 'speaker_only',
        'label'   => 'Speaker',
        'meaning' => 'The person is speaking in the passage, but someone else wrote it down.',
        'example' => 'Jesus’ words in the Gospels, which do not name their own authors.',
    ],
    [
        'key'     => 'unattributed',
        'label'   => 'Unattributed',
        'meaning' => 'The text names no author.',
        'example' => 'Psalm 121 is headed “A Song of Ascents” and names no one.',
    ],
];