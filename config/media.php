<?php
return [
    'converter' =>
        [
          'bin'     => env('CONVERTER_BIN', '/usr/bin/ffmpeg'),
          'bitrate' => env('CONVERTER_BITRATE', '320k')
        ]
];