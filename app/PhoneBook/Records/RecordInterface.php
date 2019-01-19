<?php
namespace PhoneBook\Records;

interface RecordInterface
{

    /**
     * @param string $name
     * @return mixed
     */
    public function setName($name);
    /**
     * @return string
     */
    public function getName();

    /**
     * @param float $surname
     * @return mixed
     */
    public function setSurname($surname);
    /**
     * @return float
     */
    public function getSurname();

    /**
     * @param float $phone
     * @return mixed
     */
    public function setPhone($phone);
    /**
     * @return float
     */
    public function getPhone();

    /**
     * @param float $viber
     * @return mixed
     */
    public function setViber($viber);
    /**
     * @return float
     */
    public function getViber();
}