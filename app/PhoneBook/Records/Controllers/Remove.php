<?php

namespace PhoneBook\Records\Controllers;

use PhoneBook\Services\Persistence\CantSaveException;
use PhoneBook\Services\ControllerInterface;
use PhoneBook\Records\Record;

class Remove implements ControllerInterface
{
    /**
     * @var Record
     */
    private $record;

    /**
     * @var \Katzgrau\KLogger\Logger
     */
    private $logger;

    /**
     * View constructor.
     * @param Record $record
     * @param \Katzgrau\KLogger\Logger $logger
     */
    public function __construct(
        Record $record,
        \Katzgrau\KLogger\Logger $logger
    )
    {
        $this->record = $record;
        $this->logger = $logger;
    }

    /**
     * @param $request
     * @param $response
     * @return mixed|string
     * @throws \Exception
     */
    public function execute($request, $response)
    {
        try {
            $this->record->getPersistence()->delete($request->id);
            return $response->redirect("/records");
        } catch (CantSaveException $e) {

            $this->logger->debug(
                $e->getMessage(), $e->getTrace());

            return "Sorry, the record can't be saved";
        } catch (\PhoneBook\Services\SystemException $e) {

            $this->logger->debug(
                $e->getMessage(), $e->getTrace());

            return "Oops, something went wrong, Our team is looking for solution";
        }
    }
}
