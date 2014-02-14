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

$Container->propose('Logger', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('Logger', ['Everon\Config\Manager', 'Everon\Environment']);
    $enabled = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigValue('application.logger.enabled');
    $log_directory = $Factory->getDependencyContainer()->resolve('Environment')->getLog();
    return $Factory->buildLogger($log_directory, $enabled);
});

$Container->propose('FileSystem', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('FileSystem', ['Everon\Environment']);
    $root_directory = $Factory->getDependencyContainer()->resolve('Environment')->getRoot();
    return $Factory->buildFileSystem($root_directory);
});

$Container->propose('Response', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('Response', ['Everon\Logger']);
    $Logger = $Factory->getDependencyContainer()->resolve('Logger');
    return $Factory->buildResponse($Logger->getGuid());
});

$Container->propose('ConfigManager', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('ConfigManager', ['Everon\Environment', 'Everon\Config\Loader']);
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
    $Factory->getDependencyContainer()->monitor('Router', ['Everon\Config\Manager', 'Everon\RequestValidator']);
    $RouteConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('console');
    $RequestValidator = $Factory->buildRequestValidator();
    return $Factory->buildRouter($RouteConfig, $RequestValidator);
});

$Container->propose('ModuleManager', function() use ($Factory) {
    return $Factory->buildModuleManager();
});


//avoid circular dependencies
//the logger needs ConfigManager in order to be instantiated, therefore Logger can't be auto injected into ConfigManger
$Container->afterSetup(function($Container){
    $ConfigManager = $Container->resolve('ConfigManager');
    $ConfigManager->setLogger($Container->resolve('Logger'));
});
