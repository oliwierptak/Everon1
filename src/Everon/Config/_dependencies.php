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
 * @var Application\Interfaces\Factory $EVERON_FACTORY
 */

$EVERON_FACTORY->getDependencyContainer()->propose('Logger', function() use ($EVERON_FACTORY) {
    $EVERON_FACTORY->getDependencyContainer()->monitor('Logger', ['Everon\Config\Manager', 'Everon\Environment', 'Everon\Factory']);
    $enabled = $EVERON_FACTORY->getDependencyContainer()->resolve('ConfigManager')->getConfigValue('everon.logger.enabled');
    $log_directory = $EVERON_FACTORY->getDependencyContainer()->resolve('Bootstrap')->getEnvironment()->getLog();
    return $EVERON_FACTORY->buildLogger($log_directory, $enabled);
});

$EVERON_FACTORY->getDependencyContainer()->propose('FileSystem', function() use ($EVERON_FACTORY) {
    $EVERON_FACTORY->getDependencyContainer()->monitor('FileSystem', ['Everon\Environment']);
    $root_directory = $EVERON_FACTORY->getDependencyContainer()->resolve('Bootstrap')->getEnvironment()->getRoot();
    return $EVERON_FACTORY->buildFileSystem($root_directory);
});

$EVERON_FACTORY->getDependencyContainer()->propose('Request', function() use ($EVERON_FACTORY) {
    return $EVERON_FACTORY->buildConsoleRequest($_SERVER, $_GET, $_POST, $_FILES);
});

$EVERON_FACTORY->getDependencyContainer()->propose('Router', function() use ($EVERON_FACTORY) {
    $EVERON_FACTORY->getDependencyContainer()->monitor('Router', ['Everon\Config\Manager', 'Everon\RequestValidator']);
    $RouterConfig = $EVERON_FACTORY->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('router');
    $RequestValidator = $EVERON_FACTORY->buildRequestValidator();
    return $EVERON_FACTORY->buildRouter($RouterConfig, $RequestValidator);
});

$EVERON_FACTORY->getDependencyContainer()->propose('Response', function() use ($EVERON_FACTORY) {
    $EVERON_FACTORY->getDependencyContainer()->monitor('Response', ['Everon\Logger']);
    $RequestIdentifier = $EVERON_FACTORY->getDependencyContainer()->resolve('RequestIdentifier');
    return $EVERON_FACTORY->buildResponse($RequestIdentifier->getValue());
});

$EVERON_FACTORY->getDependencyContainer()->propose('ConfigManager', function() use ($EVERON_FACTORY) {
    $EVERON_FACTORY->getDependencyContainer()->monitor('ConfigManager', ['Everon\Environment', 'Everon\Config\Loader']);
    /**
     * @var \Everon\Interfaces\Environment $Environment
     */
    $Environment = $EVERON_FACTORY->getDependencyContainer()->resolve('Bootstrap')->getEnvironment();
    $Loader = $EVERON_FACTORY->buildConfigLoader($Environment->getConfig(), $Environment->getConfigFlavour());
    $CacheLoader = $EVERON_FACTORY->buildConfigCacheLoader($Environment->getCacheConfig());
    return $EVERON_FACTORY->buildConfigManager($Loader, $CacheLoader);
});

$EVERON_FACTORY->getDependencyContainer()->propose('ModuleManager', function() use ($EVERON_FACTORY) {
    return $EVERON_FACTORY->buildModuleManager();
});

$EVERON_FACTORY->getDependencyContainer()->propose('DomainMapper', function() use ($EVERON_FACTORY) {
    $EVERON_FACTORY->getDependencyContainer()->monitor('DomainMapper', ['Everon\Config\Manager']);
    $DomainConfig = $EVERON_FACTORY->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('domain');
    return $EVERON_FACTORY->buildDomainMapper($DomainConfig->toArray());
});

$EVERON_FACTORY->getDependencyContainer()->propose('DomainManager', function() use ($EVERON_FACTORY) {
    $EVERON_FACTORY->getDependencyContainer()->monitor('DomainManager', ['Everon\DataMapper\Manager']);
    $DataMapperManager = $EVERON_FACTORY->getDependencyContainer()->resolve('DataMapperManager');
    return $EVERON_FACTORY->buildDomainManager($DataMapperManager);
});

$EVERON_FACTORY->getDependencyContainer()->propose('DataMapperManager', function() use ($EVERON_FACTORY) {
    $EVERON_FACTORY->getDependencyContainer()->monitor('DataMapperManager', ['Everon\DataMapper\Connection\Manager', 'Everon\Domain\Mapper']);
    $ConnectionManager = $EVERON_FACTORY->getDependencyContainer()->resolve('ConnectionManager');
    $DomainMapper = $EVERON_FACTORY->getDependencyContainer()->resolve('DomainMapper');
    return $EVERON_FACTORY->buildDataMapperManager($ConnectionManager, $DomainMapper);
});

$EVERON_FACTORY->getDependencyContainer()->propose('ConnectionManager', function() use ($EVERON_FACTORY) {
    $EVERON_FACTORY->getDependencyContainer()->monitor('ConnectionManager', ['Everon\Config\Manager']);
    $DatabaseConfig = $EVERON_FACTORY->getDependencyContainer()->resolve('ConfigManager')->getDatabaseConfig();
    return $EVERON_FACTORY->buildConnectionManager($DatabaseConfig);
});

$EVERON_FACTORY->getDependencyContainer()->propose('EmailManager', function() use ($EVERON_FACTORY) {
    return $EVERON_FACTORY->buildEmailManager();
});

$EVERON_FACTORY->getDependencyContainer()->propose('EventManager', function() use ($EVERON_FACTORY) {
    return $EVERON_FACTORY->buildEventManager();
});

$EVERON_FACTORY->getDependencyContainer()->register('TaskManager', function() use ($EVERON_FACTORY) {
    return $EVERON_FACTORY->buildTaskManager();
});

//xxx
//avoid circular dependencies
//the logger needs ConfigManager in order to be instantiated, therefore Logger can't be auto injected into ConfigManager
$EVERON_FACTORY->getDependencyContainer()->afterSetup(function() use ($EVERON_FACTORY) {
    $ConfigManager = $EVERON_FACTORY->getDependencyContainer()->resolve('ConfigManager');
    $ConfigManager->setLogger($EVERON_FACTORY->getDependencyContainer()->resolve('Logger'));
});
