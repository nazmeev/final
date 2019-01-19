<?php

namespace PhoneBook\Records;

use PhoneBook\Services\Model\AbstractModel;

class Record extends AbstractModel implements RecordInterface
{
    /**
     * @var string
     */
    protected $persistenceClass = "PhoneBook\Records\Persistence\Records";

    /**
     * @field
     * @primary
     * @var integer
     */
    protected $id;

    /**
     * @field
     * @var string
     */
    protected $name;

    /**
     * @field
     * @var string
     */
    protected $surname;

    /**
     * @field
     * @var string
     */
    protected $phone;

    /**
     * @field
     * @var string
     */
    protected $viber;

    /**
     * @field
     * @var string
     */
    protected $organization;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }
    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
    /**
     * @param $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getViber()
    {
        return $this->viber;
    }
    /**
     * @param string $viber
     */
    public function setViber($viber)
    {
        $this->viber = $viber;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }
    /**
     * @param string $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }
}