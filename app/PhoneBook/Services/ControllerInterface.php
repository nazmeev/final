<?php

namespace PhoneBook\Services;

interface ControllerInterface
{
    public function execute($request, $response);
}