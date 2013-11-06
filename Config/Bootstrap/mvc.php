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

$Bootstrap->getClassLoader()->add('Everon\Model', $Environment->getModel());
$Bootstrap->getClassLoader()->add('Everon\Mvc\Controller', $Environment->getController());

//replace default Router
$Container->register('Router', function() use ($Factory) {
    $RouteConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getRouterConfig();
    $RouterValidator = $Factory->buildRouterValidator();
    return $Factory->buildRouter($RouteConfig, $RouterValidator);
});

//replace default Request
$Container->register('Request', function() use ($Factory) {
    return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
});
