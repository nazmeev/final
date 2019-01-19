<?php

return [
    "log.errorFile" => DOCKROOT . '/var/log',

    "database" => [
        "dbtype" => "mysql",
        "dbname" => "phonebook",
        "user" => "root",
        "password" => "1",
    ],


    \Katzgrau\KLogger\Logger::class => DI\create()
        ->constructor(DI\get('log.errorFile')),


    PhoneBook\Services\Database::class => DI\create()
        ->constructor(DI\get('database')),

];