<?php

return [

    'login' => [
        'id' => env('LINE_LOGIN_CHANNEL_ID'),
        'secret' => env('LINE_LOGIN_CHANNEL_SECRET'),
    ],

    'messaging' => [
        'id' => env('LINE_MESSAGING_CHANNEL_ID'),
        'secret' => env('LINE_MESSAGING_CHANNEL_SECRET'),
        'token' => env('LINE_MESSAGING_CHANNEL_ACCESS_TOKEN'),
    ],

    'host' => [
        'login' => env('LINE_LOGIN_ACCESS_HOST', 'https://access.line.me'),
        'api' => env('LINE_LOGIN_API_HOST', 'https://api.line.me'),
    ],


];
