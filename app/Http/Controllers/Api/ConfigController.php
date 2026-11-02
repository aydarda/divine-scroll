<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    // GET /api/v1/config: what the app needs to know when it starts.
    public function show()
    {
        return response()->json([
            'app_name'      => config('app.name'),
            'site_url'      => rtrim(config('app.url'), '/'),
            'comments_mode' => config('contributions.comments', 'open'), // open | review | off
            'privacy_url'   => route('privacy'),
            'guidelines_url'=> route('guidelines'),
            'filters'       => [
                ['key' => 'all',          'label' => 'All voices'],
                ['key' => 'stated',       'label' => 'Stated in text'],
                ['key' => 'traditional',  'label' => 'Traditional'],
                ['key' => 'speaker',      'label' => 'Speaker, not author'],
                ['key' => 'unattributed', 'label' => 'Unattributed'],
            ],
            'limits'        => [
                'comment_max'  => 1000,
                'note_max'     => 5000,
                'password_min' => 10,
                'suggestion_max_verses' => 12,
            ],
        ]);
    }
}
