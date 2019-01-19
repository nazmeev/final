<?php

namespace PhoneBook\Services\Model;

abstract class AbstractModel implements PersistebleEntityInterface
{
    /**
     * @var string
     */
    protected $persistenceClass;

    /**
     * @var \PhoneBook\Services\Persistence\Resource
     */
    protected $persistence = null;

    /**
     * @return mixed|\PhoneBook\Services\Persistence\Resource|string
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getPersistence()
    {
        if ($this->persistence === null) {
            $this->persistence = \PhoneBook\Services\DiContainer::getInstance()
                ->get($this->persistenceClass);
            $this->persistence->setModel($this);
            $this->persistence->initTable();
        }

        return $this->persistence;
    }
}