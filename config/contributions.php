<?php

return [
    // How new comments are handled. Change COMMENTS_MODE in .env, then run: php artisan config:cache
    //   open   = comments appear straight away; a moderator can remove any of them (default)
    //   review = comments wait for a moderator's approval before anyone else sees them
    //   off    = commenting is switched off; existing comments stay visible
    'comments' => env('COMMENTS_MODE', 'open'),
];
