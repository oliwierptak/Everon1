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

$Container->propose('Logger', function() use ($Factory) {
    $enabled = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigValue('application.logger.enabled');
    $log_directory = $Factory->getDependencyContainer()->resolve('Environment')->getLog();
    return $Factory->buildLogger($log_directory, $enabled);
});

$Container->propose('Response', function() use ($Factory) {
    $Logger = $Factory->getDependencyContainer()->resolve('Logger');
    $Headers = $Factory->buildHttpHeaderCollection();
    return $Factory->buildResponse($Logger->getGuid(), $Headers);
});

$Container->propose('ConfigManager', function() use ($Factory) {
    /**
     * @var \Everon\Interfaces\Environment $Environment
     */
    $Environment = $Factory->getDependencyContainer()->resolve('Environment');
    $config_cache_directory = $Environment->getCacheConfig();
    $Loader = $Factory->buildConfigLoader($Environment->getConfig(), $config_cache_directory);
    return $Factory->buildConfigManager($Loader);
});

$Container->propose('Request', function() use ($Factory) {
    return $Factory->buildConsoleRequest($_SERVER, $_GET, $_POST, $_FILES);
});

$Container->propose('Router', function() use ($Factory) {
    $RouteConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('console');
    $RequestValidator = $Factory->buildRequestValidator();
    return $Factory->buildRouter($RouteConfig, $RequestValidator);
});