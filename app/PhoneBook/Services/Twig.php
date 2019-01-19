<?php

namespace PhoneBook\Services;

class Twig
{

    const LOG_PATH = DOCKROOT.'/templates';

    public function getTwig()
    {
        $loader = new \Twig_Loader_Filesystem(self::LOG_PATH);
        $twig = new \Twig_Environment($loader, array(
            'cache'       => 'compilation_cache',
            'auto_reload' => true
        ));
        return $twig;

    }
}
