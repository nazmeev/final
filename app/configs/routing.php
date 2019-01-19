<?php

return [
    'routers' => [
        [
            'method' => "GET",
            'path' => "/migrate/",
            'className' => "\PhoneBook\Services\Database\MigrateController"
        ],
        [
            'method' => "GET",
            'path' => "/",
            'className' => "\PhoneBook\Records\Controllers\Preview"
        ],
        [
            'method' => "GET",
            'path' => "/records",
            'className' => "\PhoneBook\Records\Controllers\Preview"
        ],
        [
            'method' => "POST",
            'path' => "/records",
            'className' => "\PhoneBook\Records\Controllers\Preview"
        ],
        [
            'method' => "GET",
            'path' => "/record/view/[i:id]",
            'className' => "\PhoneBook\Records\Controllers\View"
        ],
        [
            'method' => "GET",
            'path' => "/record/edit/[i:id]",
            'className' => "\PhoneBook\Records\Controllers\Edit"
        ],
        [
            'method' => "GET",
            'path' => "/record/create",
            'className' => "\PhoneBook\Records\Controllers\Create"
        ],
        [
            'method' => "POST",
            'path' => "/record/save",
            'className' => "\PhoneBook\Records\Controllers\Save"
        ],
        [
            'method' => "GET",
            'path' => "/record/remove/[i:id]",
            'className' => "\PhoneBook\Records\Controllers\Remove"
        ],
    ],

    \PhoneBook\Services\Router::class => DI\create()
        ->constructor(DI\get('routers'))
];