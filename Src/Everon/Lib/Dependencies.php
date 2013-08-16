<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

/**
 * @var Interfaces\DependencyContainer $Container
 * @var Interfaces\Factory $Factory
 */
if (isset($Container) === false) {
    $Container = new Dependency\Container();
}

if (isset($Factory) === false) {
    $Factory = new Factory($Container);
}

$Container->propose('Environment', function() use ($Factory) {
    return $Factory->buildEnvironment(PROJECT_ROOT);
});

$Container->propose('Logger', function() use ($Factory) {
    $log_directory = $Factory->getDependencyContainer()->resolve('Environment')->getLog();
    return $Factory->buildLogger($log_directory);
});

$Container->propose('Request', function() use ($Factory) {
    return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
});

$Container->propose('Response', function() use ($Factory) {
    $Headers = $Factory->buildHttpHeaderCollection();
    return $Factory->buildResponse($Headers);
});

$Container->propose('ConfigManager', function() use ($Factory) {
    /**
     * @var \Everon\Interfaces\Environment $Environment
     */
    $Environment = $Factory->getDependencyContainer()->resolve('Environment');
    $config_cache_directory = $Environment->getCacheConfig();
    $Matcher = $Factory->buildConfigExpressionMatcher();
    $Loader = $Factory->buildConfigLoader($Environment->getConfig(), $config_cache_directory);
    return $Factory->buildConfigManager($Loader, $Matcher);
});

$Container->propose('Router', function() use ($Factory) {
    $Request = $Factory->getDependencyContainer()->resolve('Request');
    $RouteConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getRouterConfig();
    $RouterValidator = $Factory->buildRouterValidator();
    return $Factory->buildRouter($Request, $RouteConfig, $RouterValidator);
});

$Container->propose('ModelManager', function() use ($Factory) {
    $Config = $Factory->getDependencyContainer()->resolve('ConfigManager')->getApplicationConfig();
    return $Factory->buildModelManager(
        $Config->go('model')->get('manager', 'Everon')
    );
});

$Container->propose('ViewManager', function() use ($Factory) {
    $ApplicationConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getApplicationConfig();

    return $Factory->buildViewManager(
        $ApplicationConfig->go('view')->get('compilers', []),
        $Factory->getDependencyContainer()->resolve('Environment')->getViewTemplate(),
        $Factory->getDependencyContainer()->resolve('Environment')->getWebCache()
    );
});

return $Container;