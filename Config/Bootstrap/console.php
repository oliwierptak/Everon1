<?php
namespace Everon;

$BootstrapFile = new \SplFileInfo(
    implode(DIRECTORY_SEPARATOR,
        [dirname(__FILE__), '..', '..', 'Src', 'Everon', 'Lib', 'Bootstrap.php'])
);
require_once($BootstrapFile);

/**
 * @var Bootstrap $Bootstrap
 * @var Interfaces\Environment $Environment
 * @var Interfaces\DependencyContainer $Container
 * @var Interfaces\Factory $Factory
 */

$Bootstrap->getClassLoader()->add(
    'Everon\Console\Controller', $Environment->getController().'Console'.DIRECTORY_SEPARATOR
);
