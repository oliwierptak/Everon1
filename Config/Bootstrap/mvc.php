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

$Bootstrap->getClassLoader()->add('Everon\View', $Environment->getView());
$Bootstrap->getClassLoader()->add('Everon\Model', $Environment->getModel());
$Bootstrap->getClassLoader()->add('Everon\Mvc\Controller', $Environment->getController());

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

$Container->register('ModelManager', function() use ($Factory) {
    $manager_name = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigValue('application.model.manager');
    return $Factory->buildModelManager($manager_name);
});

$Container->register('ViewManager', function() use ($Factory) {
    $compilers = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigValue('application.view.compilers');

    return $Factory->buildViewManager(
        $compilers,
        $Factory->getDependencyContainer()->resolve('Environment')->getView()
    );
});