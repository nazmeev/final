<?php

namespace PhoneBook\Services;

use orm\Exceptions\OrmRuntimeException;
use orm\Query\QueryMemento;

/**
 * Class Database
 * @package PhoneBook
 */
final class Database
{

    /**
     * Database constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        try {
            QueryMemento::getInstance()
                ->addQueryData("dbname", $settings['dbname'])
                ->addQueryData("dbtype", $settings['dbtype'])
                ->addQueryData("username", $settings['user'])
                ->addQueryData("password", $settings['password']);

        } catch (\Exception $exception) {
            die (new OrmRuntimeException("Expected username and password for mysql server"));
        }
    }
}
