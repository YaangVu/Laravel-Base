<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

return [
    'connection' => env('DB_CONNECTION', 'mysql'),
    'query'      => [
        'separator'      => '__',
        'limit'          => 10,
        'nullable_value' => false
    ],
    'cache'      => [
        'ttl' => 86400 // 1 day
    ]
];
