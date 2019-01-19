<?php

namespace PhoneBook\Services\Persistence;

use orm\DataBase\Table;
use orm\DataBase\Field;
use orm\DataBase\fields\PrimaryKey;
use orm\DataBase\fields\ForeignKey;
use orm\Query\QueryMemento;
use orm\Query\QueryExecutor;
use orm\Exceptions\MigrationException;

/**
 * Class Resource
 * @package PhoneBook
 */
abstract class Resource extends Table
{

    /**
     * @var array
     */
    private $table_fields = [];

    /**
     * @var \ReflectionClass
     */
    protected $reflection_class;

    /**
     * @var
     */
    protected $model;

    /**
     * @param \PhoneBook\Services\Model\PersistebleEntityInterface $model
     */
    public function setModel(\PhoneBook\Services\Model\PersistebleEntityInterface $model)
    {
        $this->model = $model;
    }

    /**
     * Init table, set fields' types and set name of table
     */
    public function initTable()
    {
        $this->reflection_class = new \ReflectionClass($this->model);
        $this->table_fields = $this->getTableFields(true);
        if ($this->table_name == "") {
            $this->table_name = (new \ReflectionClass($this))->getShortName();
        }
    }

    /**
     * @param $value
     * @param string $field
     * @throws \Exception
     */
    public function load($value, $field = 'id')
    {
        $data = [$field => $value];
        $pdo_statement = $this->getQueryGeneratorInstance()->selectByKeys($this->table_name, array_keys($data));
        $query_executor = new QueryExecutor($pdo_statement, $data);
        $result = $query_executor->select();
        if (count($result)) {
            foreach ($result[0] as $field => $value) {
                $setterName = 'set' . ucfirst($field);
                if (method_exists($this->model, $setterName)) {
                    $this->model->$setterName($value);
                }
            }
        } else {
            throw new NotFoundException('Entity not found');
        }
    }

    /**
     * @param array $params
     * @return \PhoneBook\Services\Model\PersistebleEntityInterface[]
     */
    public function getCollection($params = [])
    {
        if (count($params)){
            $pdo_statement = $this->getQueryGeneratorInstance()
                ->selectByKeys($this->table_name, array_keys($params));
        } else {
            $pdo_statement = $this->getQueryGeneratorInstance()
                ->selectAll($this->table_name);
        }

        $query_executor = new QueryExecutor($pdo_statement, $params);
        $result = $query_executor->select();

        if (count($result)) {
            $collection = [];
            foreach ($result as $item) {
                $model = clone $this->model;
                foreach ($item as $field => $value) {
                    $setterName = 'set' . ucfirst($field);
                    if (method_exists($model, $setterName)) {
                        $model->$setterName($value);
                    }
                }
                $collection[] = $model;
            }
            return $collection;
        }

        return [];
    }

    /**
     * @throws CantSaveException
     */
    public function save()
    {
        try {
            $data = $this->getDataForSave();
            $pdo_statement = $this
                ->getQueryGeneratorInstance()
                ->insertOrUpdateIfDuplicate($this->table_name, array_keys($data));
            $query_executor = new QueryExecutor($pdo_statement, array_combine(
                array_map(function ($key) {return ":{$key}";}, array_keys($data)),
                array_values($data)));
            $this->updateValueOfPrimaryKey($query_executor->insertOrUpdate());
        } catch (\PDOException $e) {
            throw new CantSaveException($e->getMessage());
        }
    }




    /**
     * @return bool
     * @throws MigrationException
     */
    public function migrate()
    {
        try {
            $generator = $this->getCustomQueryGeneratorInstance();
            (new QueryExecutor($generator->createTable($this->table_name, $this->table_fields), []))
                ->executeSql();
            return true;
        } catch (\PDOException $e) {
            throw new MigrationException($e->getMessage());
        }
    }

    /**
     * @param bool $addPrimaryKeyFlag
     * @return array
     */
    private function getTableFields($addPrimaryKeyFlag = false)
    {
        $storage = [];
        foreach ($this->reflection_class->getProperties() as $field) {

            $fieldType = $this->getField($field);

            if ($fieldType === false) continue;

            if (!$addPrimaryKeyFlag && $fieldType instanceof PrimaryKey) {
                continue;
            }
            else if (!$addPrimaryKeyFlag && $this->table_fields[$field->name] instanceof ForeignKey) {
                $primary_key_name = array_keys(array_filter($fieldType->table_fields, function ($item) {
                    return $item instanceof PrimaryKey;
                }));
                $storage[$field->name] = $fieldType->{$primary_key_name[0]};
            }
            else {
                $storage[$field->name] = $fieldType;
            }
        }
        return $storage;
    }

    /**
     * @return array
     */
    protected function getDataForSave()
    {
        $data = [];
        foreach ($this->reflection_class->getProperties() as $field) {
            $fieldType = $this->getField($field);

            if ($fieldType === false) continue;
            $getterName = 'get' . ucfirst($field->name);
            $data[$field->name] = $this->model->$getterName();
        }

        return $data;
    }

    /**
     * @param $field
     * @return array|bool
     */
    protected function getFieldTypeByAnnotation($field)
    {
        $annotation = $field->getDocComment();
        $annotation = explode("\n", $annotation);
        $isField = false;
        $type = [];
        foreach ($annotation as $line) {

            if (strpos($line, '@') === false) {
                continue;
            }

            if (strpos($line, '@field') !== false) {
                $isField = true;
            }

            if (strpos($line, '@primary') !== false) {
                $type['type'] =  "primary";
            }

            if (strpos($line, '@var') !== false) {
                if (!isset($type['type'])){
                    $type['type'] = trim(substr($line, strpos($line, '@var')+4));
                }
            }

            if (strpos($line, '@size') !== false) {
                $type['size'] = trim(substr($line, strpos($line, '@size')+5));
            }
        }

        if ($isField) {
            return $type;
        }

        return false;
    }

    /**
     * @param $field
     * @return bool|\orm\DataBase\fields\Number|PrimaryKey|\orm\DataBase\fields\StringField
     */
    protected function getField($field) {
        $type = $this->getFieldTypeByAnnotation($field);

        if ($type === false) return false;

        $result = false;
        switch ($type['type']) {
            case 'primary':
                $result = Field::primaryKey();
                break;
            case "string":
                $size = isset($type['size']) ? $type['size']: 255;
                $result = Field::varchar($size);
                break;
            case "double":
                $size = isset($type['size']) ? $type['size']: 10;
                $result = Field::number('double', $size);
                break;
            case "float":
                $size = isset($type['size']) ? $type['size']: 10;
                $result = Field::number('float', $size);
                break;
            case "integer":
            case "int":
                $size = isset($type['size']) ? $type['size']: 10;
                $result = Field::number('int', $size);
                break;
            case "bool":
            case "boolean":
                $size = 2;
                $result = Field::number('int', $size);
                break;
        }

        return $result;
    }

    /**
     * @return mixed|\orm\Query\QueryGeneratorInterface
     */
    private function getQueryGeneratorInstance()
    {
        $generator_name =
            "orm\\Query\\" .
            ucfirst(QueryMemento::getInstance()->getStorage()["dbtype"]) .
            "QueryGenerator";
        return new $generator_name();
    }

    /**
     * @return mixed|\orm\Query\QueryGeneratorInterface
     */
    private function getCustomQueryGeneratorInstance()
    {
        return new CustomMysqlQueryGenerator();
    }

    /**
     * @param $primary_key_new_value
     */
    private function updateValueOfPrimaryKey($primary_key_new_value)
    {
        foreach ($this->table_fields as $key => $value) {
            if ($value instanceof PrimaryKey) {
                $setterName = 'set' . ucfirst($key);
                $this->model->$setterName($primary_key_new_value);
                break;
            }
        }
    }

    /**
     * @throws CantSaveException
     */
    public function delete($value, $field = 'id')
    {
        try {
            $data = [$field => $value];
            $pdo_statement = $this
                ->getQueryGeneratorInstance()
                ->delete($this->table_name, array_keys($data));
            $query_executor = new QueryExecutor($pdo_statement, array_combine(
                array_map(function ($key) {return ":{$key}";}, array_keys($data)),
                array_values($data)));
            $this->updateValueOfPrimaryKey($query_executor->insertOrUpdate());
        } catch (\PDOException $e) {
            throw new CantSaveException($e->getMessage());
        }
    }
}
