<?php

namespace PhoneBook\Services\Model;

interface PersistebleEntityInterface
{
    /**
     * @return \PhoneBook\Services\Persistence\Resource
     */
    public function getPersistence();
}