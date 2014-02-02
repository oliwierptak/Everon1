<?php
namespace Everon;

require_once(
implode(DIRECTORY_SEPARATOR,
    [dirname(__FILE__), '..', '..', 'Src', 'Everon', 'Config', 'Bootstrap.php'])    
);

/**
 * @var Bootstrap $Bootstrap
 * @var Interfaces\Environment $Environment
 * @var Interfaces\DependencyContainer $Container
 * @var Interfaces\Factory $Factory
 */

$Bootstrap->getClassLoader()->add(
    'Everon\Console\Controller', $Environment->getController().'Console'.DIRECTORY_SEPARATOR
);
