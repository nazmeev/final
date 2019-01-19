<?php

namespace PhoneBook\Records\Controllers;

use PhoneBook\Services\Persistence\NotFoundException;
use PhoneBook\Services\ControllerInterface;
use PhoneBook\Records\Record;

class Edit implements ControllerInterface
{

    /**
     * @var Record
     */
    private $record;

    /**
     * @var
     */
    private $twig;
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
        \PhoneBook\Services\Twig $twig,
        Record $record,
        \Katzgrau\KLogger\Logger $logger
    )
    {
        $this->twig = $twig;
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
            $this->record->getPersistence()->load($request->id);
            return $this->twig->getTwig()->render('edit.html', array('record' => $this->record));
        } catch (NotFoundException $e) {
            return "Sorry, the product not found";
        } catch (\PhoneBook\Services\SystemException $e) {

            $this->logger->debug(
                $e->getMessage(), $e->getTrace());

            return "Oops, something went wrong, Our team is looking for solution";
        }
    }
}
