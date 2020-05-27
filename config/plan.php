<?php

return [

    'max_number' => env('PLAN_MAX_NUMBER', 20),

    'per_page' => env('PLAN_PER_PAGE', env('PLAN_MAX_NUMBER', 20)),

    'event_min_interval' => env('PLAN_EVENT_MIN_INTERVAL', 10),

    'name_max_len' => env('PLAN_NAME_MAX_LEN', 15),

    'list_column' => explode(',', env('PLAN_LIST_COLUMN', 'id,name,created_at,updated_at')),

];
