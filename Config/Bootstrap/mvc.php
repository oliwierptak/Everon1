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

$Bootstrap->getClassLoader()->add('Everon', $Environment->getEveron());

$Bootstrap->getClassLoader()->add('Everon\Mvc\Controller', $Environment->getController());
$Bootstrap->getClassLoader()->add('Everon\Domain', $Environment->getDomain());
$Bootstrap->getClassLoader()->add('Everon\View', $Environment->getView());
$Bootstrap->getClassLoader()->add('Everon\DataMapper', $Environment->getDataMapper());

//replace default Router
$Container->register('Router', function() use ($Factory) {
    $RouteConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('router');
    $RequestValidator = $Factory->buildRequestValidator();
    return $Factory->buildRouter($RouteConfig, $RequestValidator, 'Everon\Mvc');
});

//replace default Request
$Container->register('Request', function() use ($Factory) {
    return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
});

$Container->register('ConnectionManager', function() use ($Factory) {
    $DatabaseConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('database');
    return $Factory->buildConnectionManager($DatabaseConfig);
});

$Container->register('DomainManager', function() use ($Factory) {
    $ConnectionManager = $Factory->getDependencyContainer()->resolve('ConnectionManager');
    return $Factory->buildDomainManager($ConnectionManager);
});

$Container->register('ViewManager', function() use ($Factory) {
    $compilers = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigValue('application.view.compilers');

    return $Factory->buildViewManager(
        $compilers,
        $Factory->getDependencyContainer()->resolve('Environment')->getView()
    );
});