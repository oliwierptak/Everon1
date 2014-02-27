<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

use Everon\Config;
use Everon\Domain;
use Everon\DataMapper;
use Everon\Exception;
use Everon\Interfaces;
use Everon\Module;
use Everon\View;
use Everon\Http;
use Everon\Rest;

interface Factory
{
    /**
     * @return Interfaces\DependencyContainer
     */
    function getDependencyContainer();
    
    /**
     * @param Interfaces\DependencyContainer $Container
     */
    function setDependencyContainer(Interfaces\DependencyContainer $Container);

    /**
     * @param $class_name
     * @param $Receiver
     */
    function injectDependencies($class_name, $Receiver);

    /**
     * @param $namespace
     * @param $class_name
     * @return string
     */
    function getFullClassName($namespace, $class_name);

    /**
     * @return Interfaces\Core
     * @throws Exception\Factory
     */
    function buildConsole();

    /**
     * @return Interfaces\Core
     * @throws Exception\Factory
     */
    function buildMvc();

    /**
     * @return Interfaces\Core
     * @throws Exception\Factory
     */
    function buildRestClient();

    /**
     * @return Interfaces\Core
     * @throws Exception\Factory
     */
    function buildRestServer();

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @param $versioning
     * @param string $namespace
     * @return mixed
     * @throws Exception\Factory
     */
    function buildRestRequest(array $server, array $get, array $post, array $files, $versioning, $namespace='Everon\Rest');

    /**
     * @param $guid
     * @param Http\Interfaces\HeaderCollection $Headers
     * @return Rest\Response
     * @throws Exception\Factory
     */
    function buildRestResponse($guid, Http\Interfaces\HeaderCollection $Headers);

    /**
     * @param $name
     * @param $version
     * @param $data
     * @return mixed
     * @throws Exception\Factory
     */
    function buildRestResource($name, $version, $data);

    /**
     * @param $id
     * @param $secret
     * @return Rest\ApiKey
     * @throws Exception\Factory
     */
    function buildRestApiKey($id, $secret);

    /**
     * @param $name
     * @param Config\Interfaces\LoaderItem $ConfigLoaderItem
     * @param callable $Compiler
     * @return Interfaces\Config
     * @throws Exception\Factory
     */
    function buildConfig($name, Config\Interfaces\LoaderItem $ConfigLoaderItem, \Closure $Compiler);

    /**
     * @param Config\Interfaces\Loader $Loader
     * @return Config\Manager|mixed
     * @throws Exception\Factory
     */
    function buildConfigManager(Config\Interfaces\Loader $Loader);

    /**
     * @return Config\Interfaces\ExpressionMatcher
     * @throws Exception\Factory
     */
    function buildConfigExpressionMatcher();

    /**
     * @param $config_directory
     * @param $cache_directory
     * @return Config\Loader
     * @throws Exception\Factory
     */
    function buildConfigLoader($config_directory, $cache_directory);

    /**
     * @param $filename
     * @param array $data
     * @return Config\Loader\Item
     * @throws Exception\Factory
     */
    function buildConfigLoaderItem($filename, array $data);

    /**
     * @param $class_name
     * @param Interfaces\Module $Module
     * @param string $namespace
     * @return Interfaces\Controller
     * @throws Exception\Factory
     */
    function buildController($class_name, Interfaces\Module $Module, $namespace='Everon\Controller');

    /**
     * @param array $data
     * @param string $namespace
     * @return mixed
     * @throws Exception\Factory
     */
    function buildConnectionItem(array $data, $namespace='Everon\DataMapper');

    /**
     * @param Interfaces\Config $DatabaseConfig
     * @param string $namespace
     * @return DataMapper\Interfaces\ConnectionManager
     * @throws Exception\Factory
     */
    function buildConnectionManager(Interfaces\Config $DatabaseConfig , $namespace='Everon\DataMapper');

    /**
     * @param DataMapper\Interfaces\Schema\Table $Table
     * @param DataMapper\Interfaces\Schema $Schema
     * @param string $namespace
     * @return Interfaces\DataMapper
     * @throws Exception\Factory
     */
    function buildDataMapper(DataMapper\Interfaces\Schema\Table $Table, DataMapper\Interfaces\Schema $Schema, $namespace='Everon\DataMapper');

    /**
     * @param $name
     * @param Interfaces\DataMapper $DataMapper
     * @param string $namespace
     * @return mixed
     * @throws Exception\Factory
     */
    function buildDomainRepository($name, Interfaces\DataMapper $DataMapper, $namespace='Everon\Domain');

    /**
     * @param $class_name
     * @param string $id
     * @param array $data
     * @param string $namespace
     * @return Domain\Interfaces\Entity
     * @throws Exception\Factory
     */
    function buildDomainEntity($class_name, $id, array $data, $namespace='Everon\Domain');
    
    /**
     * @param $class_name
     * @param string $namespace
     * @return mixed
     * @throws Exception\Factory
     */
    function buildDomainModel($class_name, $namespace='Everon\Domain');

    /**
     * @param DataMapper\Interfaces\ConnectionManager $ConnectionManager
     * @param string $namespace
     * @return Domain\Interfaces\Manager
     * @throws Exception\Factory
     */
    function buildDomainManager(DataMapper\Interfaces\ConnectionManager $ConnectionManager, $namespace='Everon\Domain');

    /**
     * @param DataMapper\Interfaces\Schema\Reader $Reader
     * @param DataMapper\Interfaces\ConnectionManager $ConnectionManager
     * @param string $namespace
     * @return mixed
     * @throws Exception\Factory
     */
    function buildSchema(DataMapper\Interfaces\Schema\Reader $Reader, DataMapper\Interfaces\ConnectionManager $ConnectionManager, $namespace='Everon\DataMapper');

    /**
     * @param Interfaces\PdoAdapter $PdoAdapter
     * @param string $namespace
     * @return DataMapper\Interfaces\Schema\Reader
     * @throws Exception\Factory
     */
    function buildSchemaReader(Interfaces\PdoAdapter $PdoAdapter, $namespace='Everon\DataMapper\Schema');
    
    /**
     * @param array $data
     * @param string $namespace
     * @return DataMapper\Schema\Constraint
     * @throws Exception\Factory
     */
    function buildSchemaConstraint(array $data, $namespace='Everon\DataMapper');

    /**
     * @param $name
     * @param $schema
     * @param $adapter_name
     * @param array $columns
     * @param array $primary_keys
     * @param array $unique_keys
     * @param array $foreign_keys
     * @param string $namespace
     * @return mixed
     */
    function buildSchemaTable($name, $schema, $adapter_name, array $columns, array $primary_keys, array $unique_keys, array $foreign_keys, $namespace='Everon\DataMapper\Schema');

    /**
     * @param $name
     * @param array $data
     * @return Config\Interfaces\Item
     * @throws Exception\Factory
     */
    function buildConfigItem($name, array $data);

    /**
     * @param $name
     * @param array $data
     * @return Config\Interfaces\ItemRouter
     * @throws Exception\Factory
     */
    function buildConfigItemRouter($name, array $data);

    /**
     * @param Interfaces\Config $Config
     * @param Interfaces\RequestValidator $Validator
     * @param string $namespace
     * @return Interfaces\Router
     */
    function buildRouter(Interfaces\Config $Config, Interfaces\RequestValidator $Validator, $namespace='Everon');

    /**
     * @return Interfaces\RequestValidator
     * @throws Exception\Factory
     */
    function buildRequestValidator();

    /**
     * @param $root
     * @return FileSystem
     * @throws Exception\Factory
     */
    function buildFileSystem($root);

    /**
     * @param DataMapper\Interfaces\ConnectionItem $dsn
     * @param $username
     * @param $password
     * @param $options
     * @return \PDO
     * @throws Exception\Factory
     */
    function buildPdo($dsn, $username, $password, $options);

    /**
     * @param \PDO $Pdo
     * @param DataMapper\Interfaces\ConnectionItem $ConnectionItem
     * @return Interfaces\PdoAdapter|PdoAdapter
     * @throws Exception\Factory
     */
    function buildPdoAdapter(\PDO $Pdo, DataMapper\Interfaces\ConnectionItem $ConnectionItem);

    /**
     * @param $filename
     * @param array $template_data
     * @return Interfaces\Template
     * @throws Exception\Factory
     */
    function buildTemplate($filename, array $template_data);

    /**
     * @param $template_string
     * @param array $template_data
     * @return Interfaces\TemplateContainer
     */
    function buildTemplateContainer($template_string, array $template_data);

    /**
     * @param $class_name
     * @param string $namespace
     * @return Interfaces\TemplateCompiler
     * @throws Exception\Factory
     */
    function buildTemplateCompiler($class_name, $namespace='Everon\View\Template\Compiler');

    /**
     * @param $class_name
     * @param $template_directory
     * @param array $variables
     * @param $default_extension
     * @param string $namespace
     * @return Interfaces\View
     * @throws Exception\Factory
     */
    function buildView($class_name, $template_directory, array $variables, $default_extension, $namespace='Everon\View');

    /**
     * @param Interfaces\FileSystem $FileSystem
     * @return View\Cache
     * @throws Exception\Factory
     */
    function buildViewCache(Interfaces\FileSystem $FileSystem);

    /**
     * @param array $compilers_to_init
     * @param $theme_directory
     * @param $cache_directory
     * @return Interfaces\ViewManager
     * @throws Exception\Factory
     */
    function buildViewManager(array $compilers_to_init, $theme_directory, $cache_directory);

    /**
     * @param $directory
     * @param boolean $enabled
     * @return Interfaces\Logger
     * @throws Exception\Factory
     */
    function buildLogger($directory, $enabled);

    /**
     * @param array $headers
     * @return Interfaces\Collection
     * @throws Exception\Factory
     */    
    function buildHttpHeaderCollection(array $headers=[]);

    /**
     * @param string Unique ID
     * @return Interfaces\Response
     * @throws Exception\Factory
     */
    function buildResponse($guid);

    /**
     * @param array $data
     * @return Http\HeaderCollection
     * @throws Exception\Factory
     */
    function buildHttpHeaders(array $data);

    /**
     * @param string Unique ID
     * @param Http\Interfaces\HeaderCollection $Headers
     * @return Http\Interfaces\Response
     * @throws Exception\Factory
     */
    function buildHttpResponse($guid, Http\Interfaces\HeaderCollection $Headers);

    /**
     * @param $evrid
     * @param array $data
     * @return Http\Session
     * @throws Exception\Factory
     */
    function buildHttpSession($evrid, array $data);

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @param string $namespace
     * @return Interfaces\Request
     * @throws Exception\Factory
     */
    function buildRequest(array $server, array $get, array $post, array $files, $namespace='Everon');

    /**
     * @param $app_root
     * @param $source_root
     * @return Interfaces\Environment
     * @throws Exception\Factory
     */
    function buildEnvironment($app_root, $source_root);

    /**
     * @param $name
     * @param Interfaces\Config $module_directory
     * @param Interfaces\Config $Config
     * @param Interfaces\Config $RouterConfig
     * @return Module
     * @throws Exception\Factory
     */
    function buildModule($name, $module_directory, Interfaces\Config $Config, Interfaces\Config $RouterConfig);

    /**
     * @return Module\Manager
     * @throws Exception\Factory
     */
    function buildModuleManager();

    /**
     * @param $name
     * @param string $namespace
     * @return Interfaces\Factory
     * @throws Exception\Factory
     */
    function buildFactoryWorker($name, $namespace='Everon\Module');

    /**
     * @param $url
     * @param $version
     * @param $versioning
     * @param $namespace
     * @return mixed
     * @throws Exception\Factory
     */
    function buildRestResourceManager($url, $version, $versioning, $namespace='Everon\Rest\Resource');

/*    function registerWorker(Interfaces\FactoryWorker $Worker);

    function unRegisterWorker(Interfaces\FactoryWorker $Worker);*/
}
