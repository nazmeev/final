<?php

namespace PhoneBook\Services;

class DiContainer extends Singleton
{
    /**
     * @return \DI\Container
     * @throws \Exception
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            $builder = new \DI\ContainerBuilder();
            $builder->useAutowiring(true);
            $builder->addDefinitions(DOCKROOT . '/app/configs/global.config.php');
            $builder->addDefinitions(DOCKROOT . '/app/configs/routing.php');

            static::$instance = $builder->build();
        }

        return static::$instance;
    }
}