<?php

return [
    'api/v1/login' => 'api/v1/user/login',
    'forgot-password' => 'site/forgot-password',
    'api/v1/logout' => 'api/v1/user/logout',
    'api/v1/register' => 'api/v1/user/register',
    'api/v1/profile' => 'api/v1/user/view',
    'api/v1/forgot-password' => 'api/v1/user/forgot-password',
    'api/v1/countries' => 'api/v1/user/countries',
    'POST api/v1/history' => 'api/v1/history/save',
    'GET api/v1/history' => 'api/v1/history/view',
    'api/v1/feed' => 'api/v1/history/feed',
    'POST api/v1/product' => 'api/v1/product/view',
    'api/v1/create-product' => 'api/v1/product/create-product',
    'POST api/v1/product-manufacturer' => 'api/v1/product/product-manufacturer',
    'POST api/v1/product-category' => 'api/v1/product/product-category',
    'POST api/v1/categories' => 'api/v1/product/categories',
    'api/v1/save-all-product' => 'api/v1/product/save-all-product',
    'api/v1/update-gtins' => 'api/v1/product/update-gtins',
    'POST api/v1/ratings' => 'api/v1/ratings/view',
    'api/v1/manufacturers' => 'api/v1/manufacturers/save',
    'api/v1/manufacturers-list' => 'api/v1/manufacturers/manufacturers-list',
    'POST api/v1/update-manufacturer' => 'api/v1/manufacturers/update-manufacturer',
    'api/v1/manufacturer' => 'api/v1/manufacturers/manufacturer',
    ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/default'],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'api/v1/user',
        'extraPatterns' => [
            'POST login' => 'login',
            'POST register' => 'register',
            'POST logout' => 'logout',
            'GET profile' => 'profile',
            'POST forgot-password' => 'forgot-password',
        ]
    ],
    ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/default'],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'api/v1/history',
        'extraPatterns' => [
            'POST save' => 'save',
            'GET view' => 'view',
            'GET feed' => 'feed',
        ]
    ],
];