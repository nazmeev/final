<?php

namespace PhoneBook\Services\Persistence;


use orm\DataBase\fields\DateTime;
use orm\DataBase\fields\ForeignKey;
use orm\DataBase\fields\Number;
use orm\DataBase\fields\PrimaryKey;
use orm\DataBase\fields\StringField;
use orm\Exceptions\ExceptionsMessages;
use orm\Exceptions\MigrationException;
use orm\Query\MysqlQueryGenerator;
use orm\Query\PdoAdapter;

class CustomMysqlQueryGenerator extends MysqlQueryGenerator
{

    /**
     * @var null|\PDO
     */
    private $pdo = null;

    /**
     * CustomMysqlQueryGenerator constructor.
     */
    public function __construct()
    {
        $this->pdo = PdoAdapter::getInstance()->getPdoObject();
    }

    /**
     * @param $table
     * @param $fields
     * @return bool|\PDOStatement
     * @throws MigrationException
     * @throws \ReflectionException
     */
    public function createTable($table, $fields)
    {
        $query = "set foreign_key_checks = 0;\n";
        $foreign_keys = "";
        $query .= "drop table if exists `{$table}`; create table `{$table}` (\n";
        foreach ($fields as $key => $field) {
            if ($field instanceof PrimaryKey) {
                $query .= "`{$key}` {$field->type}({$field->size}) not null primary key auto_increment,\n";
            } elseif ($field instanceof ForeignKey) {
                $query .= "`{$key}` int not null,\n";
                $current_table = new $field->table();
                $reflection = new \ReflectionClass($current_table);
                $property = $reflection->getProperty("table_name");
                $property->setAccessible(true);
                $foreign_keys .= "alter table `{$table}` add constraint `{$key}_fk` foreign key (`{$key}`)" .
                    " references `{$property->getValue($current_table)}` (`{$field->field}`) on delete " .
                    "{$field->on_delete} on update {$field->on_update};\n";
                $property->setAccessible(false);
            } elseif ($field instanceof StringField) {
                $query .= "`{$key}` {$field->type}({$field->size}),\n";
            } elseif ($field instanceof DateTime) {
                $query .= "`{$key}` {$field->type},\n";
            } elseif ($field instanceof Number) {
                $auto_increment = ($field->auto_increment) ? "auto_increment" : "";
                $query .= "`{$key}` {$field->type}({$field->size}) {$field->attribute} {$auto_increment},\n";
            } else {
                throw new MigrationException(ExceptionsMessages::unsupportedTypeOfField(gettype($field)));
            }
        }
        return $this->pdo->prepare(substr($query, 0, -2) . "\n) engine=InnoDB default charset=utf8;\n" .
            $foreign_keys . "set foreign_key_checks = 1;\n");
    }
}
