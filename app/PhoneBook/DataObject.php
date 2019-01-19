<?php

namespace PhoneBook;

class DataObject
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param string $index
     * @return mixed|null
     */
    public function getData($index)
    {
        if (isset($this->data[$index])) {
            return $this->data[$index];
        }

        return null;
    }

    /**
     * @param $index
     * @param $value
     */
    public function setData($index, $value)
    {
        $this->data[$index] = $value;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'get') == 0) {
            $index = substr($name,3);
            $index = strtolower($index);
            if (isset($this->data[$index])) {
                return $this->data[$index];
            }
        }
    }
}
