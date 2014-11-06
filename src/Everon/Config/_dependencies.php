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
 * @var Application\Interfaces\DependencyContainer $Container
 * @var Application\Interfaces\Factory $Factory
 */

$Container->propose('Logger', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('Logger', ['Everon\Config\Manager', 'Everon\Environment', 'Everon\Factory']);
    $enabled = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigValue('application.logger.enabled');
    $log_directory = $Factory->getDependencyContainer()->resolve('Bootstrap')->getEnvironment()->getLog();
    return $Factory->buildLogger($log_directory, $enabled);
});

$Container->propose('FileSystem', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('FileSystem', ['Everon\Environment']);
    $root_directory = $Factory->getDependencyContainer()->resolve('Bootstrap')->getEnvironment()->getRoot();
    return $Factory->buildFileSystem($root_directory);
});

$Container->propose('Request', function() use ($Factory) {
    return $Factory->buildConsoleRequest($_SERVER, $_GET, $_POST, $_FILES);
});

$Container->propose('Router', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('Router', ['Everon\Config\Manager', 'Everon\RequestValidator']);
    $RouterConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('router');
    $RequestValidator = $Factory->buildRequestValidator();
    return $Factory->buildRouter($RouterConfig, $RequestValidator);
});

$Container->propose('Response', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('Response', ['Everon\Logger']);
    $RequestIdentifier = $Factory->getDependencyContainer()->resolve('RequestIdentifier');
    return $Factory->buildResponse($RequestIdentifier->getValue());
});

$Container->propose('ConfigManager', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('ConfigManager', ['Everon\Environment', 'Everon\Config\Loader']);
    /**
     * @var \Everon\Interfaces\Environment $Environment
     */
    $Environment = $Factory->getDependencyContainer()->resolve('Bootstrap')->getEnvironment();
    $config_cache_directory = $Environment->getCacheConfig();
    $Loader = $Factory->buildConfigLoader($Environment->getConfig(), $config_cache_directory);
    return $Factory->buildConfigManager($Loader);
});

$Container->propose('ModuleManager', function() use ($Factory) {
    return $Factory->buildModuleManager();
});

$Container->propose('DomainMapper', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('DomainMapper', ['Everon\Config\Manager']);
    $DomainConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('domain');
    return $Factory->buildDomainMapper($DomainConfig->toArray());
});

$Container->propose('DomainManager', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('DomainManager', ['Everon\DataMapper\Manager']);
    $DataMapperManager = $Factory->getDependencyContainer()->resolve('DataMapperManager');
    return $Factory->buildDomainManager($DataMapperManager);
});

$Container->propose('DataMapperManager', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('DataMapperManager', ['Everon\DataMapper\Connection\Manager', 'Everon\Domain\Mapper']);
    $ConnectionManager = $Factory->getDependencyContainer()->resolve('ConnectionManager');
    $DomainMapper = $Factory->getDependencyContainer()->resolve('DomainMapper');
    return $Factory->buildDataMapperManager($ConnectionManager, $DomainMapper);
});

$Container->propose('ConnectionManager', function() use ($Factory) {
    $Factory->getDependencyContainer()->monitor('ConnectionManager', ['Everon\Config\Manager']);
    $DatabaseConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getDatabaseConfig();
    return $Factory->buildConnectionManager($DatabaseConfig);
});

$Container->propose('EmailManager', function() use ($Factory) {
    return $Factory->buildEmailManager();
});

$Container->propose('EventManager', function() use ($Factory) {
    return $Factory->buildEventManager();
});

$Container->register('TaskManager', function() use ($Factory) {
    return $Factory->buildTaskManager();
});

//xxx
//avoid circular dependencies
//the logger needs ConfigManager in order to be instantiated, therefore Logger can't be auto injected into ConfigManger
$Container->afterSetup(function($Container){
    $ConfigManager = $Container->resolve('ConfigManager');
    $ConfigManager->setLogger($Container->resolve('Logger'));
});
