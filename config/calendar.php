<?php

return [

    'lange' => [
        'previous' => env('CALENDAR_PREVIOUS', 1),
        'feature' => env('CALENDAR_FEATURE', 3),
    ],

    'list_column' => explode(',', env('CALENDAR_LIST_COLUMN', 'date,plan_id')),

];
