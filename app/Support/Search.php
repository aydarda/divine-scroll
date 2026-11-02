<?php

namespace App\Support;

use App\Models\Book;
use App\Models\Passage;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

// Search for the website and the app.
//   "john 3:16", "psalm 23", "1 cor 13"  -> passages at that reference
//   anything else                        -> passages containing every word, best matches first
// Words are matched against passages.search_text, which `php artisan search:index` fills in.
class Search
{
    private const MAX_CANDIDATES = 1500;
    private const MAX_RESULTS = 200;

    private static ?array $books = null;

    public static function run(string $query, int $page = 1, int $perPage = 10): array
    {
        $query = trim(mb_substr($query, 0, 100));
        $page = max(1, $page);

        if (mb_strlen($query) < 2) {
            return ['mode' => 'none', 'ids' => [], 'total' => 0, 'next_page' => null, 'voices' => []];
        }

        self::ensureFresh();

        $reference = self::referenceIds($query);

        if ($reference !== null) {
            $mode = 'reference';
            $all = $reference;
        } else {
            $mode = 'text';
            $all = self::textIds($query);
        }

        $total = count($all);

        return [
            'mode'      => $mode,
            'ids'       => array_slice($all, ($page - 1) * $perPage, $perPage),
            'total'     => $total,
            'next_page' => $total > $page * $perPage ? $page + 1 : null,
            'voices'    => self::voices($query),
        ];
    }

    public static function normalize(string $text): string
    {
        $t = mb_strtolower($text);
        $t = str_replace(['’', '‘', "'"], '', $t);
        $t = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $t);

        return trim(preg_replace('/\s+/u', ' ', $t));
    }

    // Builds and stores the searchable text for one passage.
    public static function index(Passage $p): void
    {
        $parts = [
            $p->reference,
            $p->heading,
            $p->body,
            $p->attribution?->person?->name,
            implode(' ', $p->themes ?? []),
        ];

        DB::table('passages')->where('id', $p->id)->update([
            'search_text' => self::normalize(implode(' ', array_filter($parts))),
        ]);
    }

    // Indexes passages that have none yet (new ones), a few hundred at a time.
    public static function ensureFresh(int $limit = 300): void
    {
        $stale = Passage::approved()
            ->whereNull('search_text')
            ->with(['book', 'attribution.person'])
            ->limit($limit)
            ->get();

        foreach ($stale as $p) {
            self::index($p);
        }
    }

    private static function textIds(string $query): array
    {
        $norm = self::normalize($query);
        $tokens = array_slice(array_values(array_unique(array_filter(
            explode(' ', $norm),
            fn ($t) => mb_strlen($t) >= 2
        ))), 0, 6);

        if (! $tokens) {
            return [];
        }

        $builder = Passage::approved()->whereNotNull('search_text');

        foreach ($tokens as $t) {
            $builder->where('search_text', 'like', '%' . $t . '%'); // tokens hold only letters and digits
        }

        $scored = [];

        foreach ($builder->limit(self::MAX_CANDIDATES)->get(['id', 'search_text']) as $row) {
            $text = ' ' . $row->search_text . ' ';
            $score = str_contains($text, ' ' . $norm . ' ') ? 10 : (str_contains($text, $norm) ? 6 : 0);

            foreach ($tokens as $t) {
                $score += min(3, substr_count($text, ' ' . $t . ' '));
            }

            $score += 1 / (1 + strlen($text) / 2000); // short passages edge ahead on ties
            $scored[$row->id] = $score;
        }

        arsort($scored);

        return array_slice(array_keys($scored), 0, self::MAX_RESULTS);
    }

    private static function referenceIds(string $query): ?array
    {
        if (! preg_match('/^(.+?)\s*(\d{1,3})(?:\s*[:.]\s*(\d{1,3})(?:\s*[-–—]\s*(\d{1,3}))?)?$/u', $query, $m)) {
            return null;
        }

        $book = self::findBook(trim($m[1]));

        if (! $book) {
            return null;
        }

        $chapter = (int) $m[2];
        $from = isset($m[3]) && $m[3] !== '' ? (int) $m[3] : null;
        $to = isset($m[4]) && $m[4] !== '' ? (int) $m[4] : $from;

        return Passage::approved()
            ->where('book_id', $book['id'])
            ->where('chapter', $chapter)
            ->when($from !== null, fn ($q) => $q->where('verse_start', '<=', $to)->where('verse_end', '>=', $from))
            ->orderBy('verse_start')
            ->pluck('id')
            ->all();
    }

    private static function findBook(string $name): ?array
    {
        $norm = preg_replace('/[^a-z0-9]/', '', mb_strtolower($name));

        if ($norm === '') {
            return null;
        }

        $aliases = [
            'ps' => 'psalms', 'psalm' => 'psalms', 'psa' => 'psalms', 'prov' => 'proverbs',
            'eccl' => 'ecclesiastes', 'ecc' => 'ecclesiastes', 'song' => 'songofsolomon', 'songs' => 'songofsolomon',
            'sos' => 'songofsolomon', 'isa' => 'isaiah', 'jer' => 'jeremiah', 'lam' => 'lamentations',
            'ezek' => 'ezekiel', 'dan' => 'daniel', 'hos' => 'hosea', 'matt' => 'matthew', 'mt' => 'matthew',
            'mk' => 'mark', 'lk' => 'luke', 'jn' => 'john', 'rom' => 'romans', '1cor' => '1corinthians',
            '2cor' => '2corinthians', 'gal' => 'galatians', 'eph' => 'ephesians', 'phil' => 'philippians',
            'col' => 'colossians', '1thess' => '1thessalonians', '2thess' => '2thessalonians',
            '1tim' => '1timothy', '2tim' => '2timothy', 'heb' => 'hebrews', 'jas' => 'james',
            '1pet' => '1peter', '2pet' => '2peter', '1jn' => '1john', '2jn' => '2john', '3jn' => '3john',
            'rev' => 'revelation',
        ];

        $norm = $aliases[$norm] ?? $norm;

        self::$books ??= Book::orderBy('position')->get(['id', 'name'])->map(fn ($b) => [
            'id'   => $b->id,
            'norm' => preg_replace('/[^a-z0-9]/', '', mb_strtolower($b->name)),
        ])->all();

        foreach (self::$books as $b) {
            if ($b['norm'] === $norm) {
                return $b;
            }
        }

        if (strlen($norm) >= 3) {
            foreach (self::$books as $b) {
                if (str_starts_with($b['norm'], $norm)) {
                    return $b;
                }
            }
        }

        return null;
    }

    private static function voices(string $query): array
    {
        $norm = self::normalize($query);

        if (mb_strlen($norm) < 2) {
            return [];
        }

        return Person::whereRaw('lower(name) like ?', ['%' . $norm . '%'])
            ->orderBy('name')
            ->limit(5)
            ->get(['slug', 'name', 'handle', 'kind'])
            ->all();
    }
}
