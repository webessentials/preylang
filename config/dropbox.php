<?php

return [
    'impact_file_path' => 'Impacts',
    'rootDir' => '/',
    'app_key' => env('DROPBOX_APP_KEY'),
    'app_secret' => env('DROPBOX_APP_SECRET'),
    'access_token' => env('DROPBOX_APP_ACCESS_TOKEN'),
    'fileTypes' => ['jpeg', 'jpg', '3gp', 'mp3', 'amr', 'mpga'],
    'audioFiles' => ['3gp', 'mp3', 'amr', 'wav', 'mpga', 'ogg', 'oga', 'wma', 'weba', 'mp4'],
];
