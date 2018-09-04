<?php

return [
    'adminEmail' => 'admin@example.com',
    'salt' => '',
    'apiAuthCredentials' => [
        'facebook' => [
            'default_graph_version' => 'v2.8',
            'app_id' => '',
            'app_secret' => '',
        ],
        'twitter' => [
            'consumerKey' => '',
            'consumerSecret' => '',
        ],
        'tables' => [
            'user' => 'user',
            'userDevice' => 'userDevice'
        ],
        'jwtExp' => 60*60*24*30 //month
    ],
    'pages' => 50,
    'ucatApiUrl' => '',
    'authKey' => ''
];
