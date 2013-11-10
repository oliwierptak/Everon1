<?php
namespace Everon;

require_once(
    implode(DIRECTORY_SEPARATOR,
        [dirname(__FILE__), '..', '..', 'Src', 'Everon', 'Lib', 'Bootstrap.php'])  
);

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
    $RouteConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('router');
    $RouterValidator = $Factory->buildRouterValidator();
    return $Factory->buildRouter($RouteConfig, $RouterValidator, 'Everon\Mvc');
});

//replace default Request
$Container->register('Request', function() use ($Factory) {
    return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
});
