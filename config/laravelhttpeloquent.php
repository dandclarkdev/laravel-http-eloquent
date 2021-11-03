<?php

use App\Models\Comment;

return [
    'services' => [
        'placeholder' => [
            'base_url' => 'https://jsonplaceholder.typicode.com',
            'models' => [
                'comments' => Comment::class
            ]
        ]
    ]
];