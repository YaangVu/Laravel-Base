<?php

return [
    'limit'      => 10,
    'page'       => 1,
    'connection' => env('DB_CONNECTION', 'mysql'),
    'query'      => [
        'delimiter' => env('BASE_QUERY_DELIMITER', '__')
    ]
];
