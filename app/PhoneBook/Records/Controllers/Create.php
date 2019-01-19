<?php

namespace PhoneBook\Records\Controllers;

use PhoneBook\Services\ControllerInterface;
use PhoneBook\Records\Record;

class Create implements ControllerInterface
{

    /**
     * @var \PhoneBook\Services\Twig
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
        \Katzgrau\KLogger\Logger $logger
    )
    {
        $this->twig = $twig;
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
            return $this->twig->getTwig()->render('create.html', array('record' => ''));

        } catch (\PhoneBook\Services\SystemException $e) {

            $this->logger->debug(
                $e->getMessage(), $e->getTrace());

            return "Oops, something went wrong, Our team is looking for solution";
        }
    }
}
