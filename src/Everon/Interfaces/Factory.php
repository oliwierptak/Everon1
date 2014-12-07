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
use Everon\FileSystem;
use Everon\Module;
use Everon\View;
use Everon\Http;
use Everon\Rest;
use Everon\Email;

interface Factory
{
    /**
     * @param $class_name
     * @param $Receiver
     */
    function injectDependencies($class_name, $Receiver);

    /**
     * @return Interfaces\DependencyContainer
     */
    function getDependencyContainer();

    /**
     * @param Interfaces\DependencyContainer $Container
     */
    function setDependencyContainer(Interfaces\DependencyContainer $Container);

    /**
     * @param $namespace
     * @param $class_name
     * @return string
     */
    function getFullClassName($namespace, $class_name);

    /**
     * @param $class
     * @throws Exception\Factory
     */
    function classExists($class);

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
     * @return Rest\CurlAdapter
     * @throws Exception\Factory
     */
    function buildRestCurlAdapter();

    /**
     * @param Interfaces\Collection $FilterDefinition
     * @param string $namespace
     * @return mixed
     * @throws Exception\Factory
     */
    //function buildRestFilter(Interfaces\Collection $FilterDefinition, $namespace='Everon\Rest');

    /**
     * @param Rest\Interfaces\ResourceHref $Href
     * @param Rest\Interfaces\CurlAdapter $CurlAdapter
     * @return Interfaces\Core|Rest\Client
     */
    function buildRestClient(Rest\Interfaces\ResourceHref $Href, Rest\Interfaces\CurlAdapter $CurlAdapter);

    /**
     * @param string $namespace
     * @return Rest\Interfaces\Filter
     * @throws Exception\Factory
     */
    function buildRestFilter($namespace='Everon\Rest');

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
     * @return Rest\Interfaces\Request
     * @throws Exception\Factory
     */
    function buildRestRequest(array $server, array $get, array $post, array $files, $versioning, $namespace='Everon\Rest');

    /**
     * @param $guid
     * @param Http\Interfaces\HeaderCollection $HeaderCollection
     * @param Http\Interfaces\CookieCollection $CookieCollection
     * @param string $namespace
     * @return Rest\Response
     * @throws Exception\Factory
     */
    function buildRestResponse($guid, Http\Interfaces\HeaderCollection $HeaderCollection, Http\Interfaces\CookieCollection $CookieCollection, $namespace='Everon\Rest');

    /**
     * @param $name
     * @param $version
     * @param Rest\Interfaces\ResourceHref $Href
     * @param $resource_name
     * @param Domain\Interfaces\Entity $Entity
     * @param string $namespace
     * @return Rest\Interfaces\Resource
     * @throws Exception\Factory
     */
    function buildRestResource($name, $version, Rest\Interfaces\ResourceHref $Href, $resource_name, Domain\Interfaces\Entity $Entity, $namespace='Everon\Rest\Resource');

    /**
     * @param $name
     * @param Rest\Interfaces\ResourceHref $Href
     * @param Interfaces\Collection $Collection
     * @param Interfaces\Paginator $Paginator
     * @param string $namespace
     * @return Rest\Interfaces\ResourceCollection
     */
    function buildRestCollectionResource($name, Rest\Interfaces\ResourceHref $Href, Interfaces\Collection $Collection, Interfaces\Paginator $Paginator, $namespace='Everon\Rest\Resource');

    /**
     * @param $url
     * @param $version
     * @param $versioning
     * @param string $namespace
     * @return Rest\Interfaces\ResourceHref
     * @throws Exception\Factory
     */
    function buildRestResourceHref($url, $version, $versioning, $namespace='Everon\Rest\Resource');

    /**
     * @param Rest\Interfaces\Request $Request
     * @param string $namespace
     * @return Rest\Interfaces\ResourceNavigator
     * @throws Exception\Factory
     */
    function buildRestResourceNavigator(Rest\Interfaces\Request $Request, $namespace='Everon\Rest\Resource');

    /**
     * @param $id
     * @param $secret
     * @return Rest\ApiKey
     * @throws Exception\Factory
     */
    function buildRestApiKey($id, $secret);

    /**
     * Class name is based on filename from ConfigLoaderItem, eg. /var/www/.../Module/_Core/Config/router.ini
     * will become Everon\Config\Router
     *
     * @param $name
     * @param $filename
     * @param array $data
     * @internal param \Everon\Config\Interfaces\LoaderItem $ConfigLoaderItem
     * @return Interfaces\Config
     */
    function buildConfig($name, $filename, array $data);

    /**
     * @param Config\Interfaces\Loader $Loader
     * @param \Everon\FileSystem\Interfaces\CacheLoader $CacheLoader
     * @param string $namespace
     * @return Config\Manager|mixed
     */
    function buildConfigManager(Config\Interfaces\Loader $Loader, FileSystem\Interfaces\CacheLoader $CacheLoader, $namespace = 'Everon\Config');

    /**
     * @return Config\Interfaces\ExpressionMatcher
     * @throws Exception\Factory
     */
    function buildConfigExpressionMatcher();

    /**
     * @param $config_directory
     * @param string $namespace
     * @return Config\Loader
     */
    function buildConfigLoader($config_directory, $namespace = 'Everon\Config');

    /**
     * @param $cache_directory
     * @param string $namespace
     * @return FileSystem\Interfaces\CacheLoader
     */
    function buildConfigCacheLoader($cache_directory, $namespace = 'Everon\FileSystem\CacheLoader');

    /**
     * @param $class_name
     * @param Module\Interfaces\Module $Module
     * @param string $namespace
     * @return Interfaces\Controller
     * @throws Exception\Factory
     */
    function buildController($class_name, Module\Interfaces\Module $Module, $namespace='Everon\Controller');

    /**
     * @param array $data
     * @param string $namespace
     * @return DataMapper\Interfaces\ConnectionItem
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
     * @param string $name
     * @param DataMapper\Interfaces\Schema\Table $Table
     * @param DataMapper\Interfaces\Schema $Schema
     * @param string $namespace
     * @return Interfaces\DataMapper
     * @throws Exception\Factory
     */
    function buildDataMapper($name, DataMapper\Interfaces\Schema\Table $Table, DataMapper\Interfaces\Schema $Schema, $namespace='Everon\DataMapper');

    /**
     * @param DataMapper\Interfaces\ConnectionManager $ConnectionManager
     * @param Domain\Interfaces\Mapper $DomainMapper
     * @param string $namespace
     * @return DataMapper\Interfaces\Manager
     * @throws Exception\Factory
     */
    function buildDataMapperManager(DataMapper\Interfaces\ConnectionManager $ConnectionManager, Domain\Interfaces\Mapper $DomainMapper, $namespace='Everon\DataMapper');

    /**
     * @param string $namespace
     * @returns DataMapper\Interfaces\Criteria
     * @throws Exception\Factory
     */
    function buildCriteria($namespace='Everon\DataMapper');

    /**
     * @param string $namespace
     * @returns DataMapper\Interfaces\Criteria\Builder
     * @throws Exception\Factory
     */
    function buildCriteriaBuilder($namespace='Everon\DataMapper\Criteria');

    /**
     * @param DataMapper\Interfaces\Criteria $Criteria
     * @param $glue
     * @param string $namespace
     * @return DataMapper\Interfaces\Criteria\Container
     */
    function buildCriteriaContainer(DataMapper\Interfaces\Criteria $Criteria, $glue, $namespace='Everon\DataMapper\Criteria');

    /**
     * @param $type
     * @param string $namespace
     * @returns DataMapper\Interfaces\Criteria\Operator
     * @throws Exception\Factory
     */
    function buildCriteriaOperator($type, $namespace='Everon\DataMapper\Criteria\Operator');

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @param string $namespace
     * @return DataMapper\Interfaces\Criteria\Criterium
     */
    function buildCriteriaCriterium($column, $operator, $value, $namespace = 'Everon\DataMapper\Criteria');

    /**
     * @param $sql
     * @param array $parameters
     * @param string $namespace
     * @return DataMapper\Interfaces\SqlPart
     */
    function buildDataMapperSqlPart($sql, array $parameters, $namespace = 'Everon\DataMapper\Criteria');

    /**
     * @param $name
     * @param Interfaces\DataMapper $DataMapper
     * @param string $namespace
     * @return Domain\Interfaces\Repository
     * @throws Exception\Factory
     */
    function buildDomainRepository($name, Interfaces\DataMapper $DataMapper, $namespace='Everon\Domain');

    /**
     * @param $class_name
     * @param string $id_field
     * @param array $data
     * @param string $namespace
     * @return Domain\Interfaces\Entity
     * @throws Exception\Factory
     */
    function buildDomainEntity($class_name, $id_field, array $data, $namespace='Everon\Domain');

    /**
     * @param array $mappings
     * @param string $namespace
     * @return Domain\Interfaces\Mapper
     * @throws Exception\Factory
     */
    function buildDomainMapper(array $mappings, $namespace='Everon\Domain');

    /**
     * @param $class_name
     * @param string $namespace
     * @return Domain\Interfaces\Model
     * @throws Exception\Factory
     */
    function buildDomainModel($class_name, $namespace='Everon\Domain');

    /**
     * @param DataMapper\Interfaces\Manager $DataMapperManager
     * @param string $namespace
     * @return Domain\Interfaces\Manager
     * @throws Exception\Factory
     */
    function buildDomainManager(DataMapper\Interfaces\Manager $DataMapperManager, $namespace='Everon\Domain');

    /**
     * @param $name
     * @param Domain\Interfaces\Entity $Entity
     * @param \Everon\Domain\Interfaces\RelationMapper $RelationMapper
     * @param string $namespace
     * @return Domain\Interfaces\Relation
     */
    function buildDomainRelation($name, Domain\Interfaces\Entity $Entity, Domain\Interfaces\RelationMapper $RelationMapper, $namespace = 'Everon\Domain');

    /**
     * @param string $type
     * @param string $domain_name
     * @param string $column
     * @param string $mapped_by
     * @param string $inversed_by
     * @param bool $is_virtual
     * @param string $namespace
     * @return Domain\Interfaces\RelationMapper
     */
    function buildDomainRelationMapper($type, $domain_name, $column = null, $mapped_by = null, $inversed_by = null, $is_virtual = false, $namespace = 'Everon\Domain\Relation');

    /**
     * @param DataMapper\Interfaces\Schema\Reader $Reader
     * @param DataMapper\Interfaces\ConnectionManager $ConnectionManager
     * @param Domain\Interfaces\Mapper $DomainMapper
     * @param \Everon\FileSystem\Interfaces\CacheLoader $CacheLoader
     * @param string $namespace
     * @return DataMapper\Interfaces\Schema
     * @throws Exception\Factory
     */
    function buildSchema(DataMapper\Interfaces\Schema\Reader $Reader, 
                         DataMapper\Interfaces\ConnectionManager $ConnectionManager, 
                         Domain\Interfaces\Mapper $DomainMapper,
                         FileSystem\Interfaces\CacheLoader $CacheLoader, 
                         $namespace = 'Everon\DataMapper');

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
     * @param $database_timezone
     * @param $name
     * @param $schema
     * @param $adapter_name
     * @param array $columns
     * @param array $primary_keys
     * @param array $unique_keys
     * @param array $foreign_keys
     * @param Collection $ColumnValidators
     * @param Domain\Interfaces\Mapper $DomainMapper
     * @param string $namespace
     * @return DataMapper\Interfaces\Schema\Table
     * @throws Exception\Factory
     */
    function buildSchemaTableAndDependencies($database_timezone, $name, $schema, $adapter_name, array $columns, array $primary_keys, array $unique_keys, array $foreign_keys, Interfaces\Collection $ColumnValidators, Domain\Interfaces\Mapper $DomainMapper, $namespace = 'Everon\DataMapper');

    /**
     * @param $adapter_name
     * @param $database_timezone
     * @param array $data
     * @param $is_pk
     * @param $is_unique
     * @param Collection $ColumnValidators
     * @param string $namespace
     * @return DataMapper\Interfaces\Schema\Column
     * @throws Exception\Factory
     */
    function buildSchemaColumn($adapter_name, $database_timezone, array $data, $is_pk, $is_unique, Interfaces\Collection $ColumnValidators, $namespace = 'Everon\DataMapper\Schema');

    /**
     * @param array $foreign_key_data
     * @param string $namespace
     * @return DataMapper\Interfaces\Schema\ForeignKey
     * @throws Exception\Factory
     */
    function buildSchemaForeignKey(array $foreign_key_data, $namespace='Everon\DataMapper\Schema');

    /**
     * @param array $primary_key_data
     * @param string $namespace
     * @return DataMapper\Interfaces\Schema\PrimaryKey
     * @throws Exception\Factory
     */
    function buildSchemaPrimaryKey(array $primary_key_data, $namespace='Everon\DataMapper\Schema');

    /**
     * @param array $unique_key_data
     * @param string $namespace
     * @return DataMapper\Interfaces\Schema\UniqueKey
     * @throws Exception\Factory
     */
    function buildSchemaUniqueKey(array $unique_key_data, $namespace='Everon\DataMapper\Schema');

    /**
     * @param $name
     * @param $schema
     * @param array $column_list
     * @param array $primary_key_list
     * @param array $unique_key_list
     * @param array $foreign_key_list
     * @param Domain\Interfaces\Mapper $DomainMapper
     * @param string $namespace
     * @return DataMapper\Interfaces\Schema\Table
     * @throws Exception\Factory
     */
    function buildSchemaTable($name, $schema, array $column_list, array $primary_key_list, array $unique_key_list, array $foreign_key_list, Domain\Interfaces\Mapper $DomainMapper, $namespace = 'Everon\DataMapper\Schema');

    /**
     * @param $original_name
     * @param $name
     * @param $schema
     * @param array $column_list
     * @param array $primary_key_list
     * @param array $unique_key_list
     * @param array $foreign_key_list
     * @param Domain\Interfaces\Mapper $DomainMapper
     * @param string $namespace
     * @return DataMapper\Interfaces\Schema\View
     * @throws Exception\Factory
     */
    function buildSchemaView($original_name, $name, $schema, array $column_list, array $primary_key_list,  array $unique_key_list, array $foreign_key_list, Domain\Interfaces\Mapper $DomainMapper, $namespace='Everon\DataMapper');

    /**
     * @param $type
     * @param $adapter_name
     * @param string $namespace
     * @return DataMapper\Interfaces\Schema\Column\Validator
     * @throws Exception\Factory
     */
    function buildSchemaColumnValidator($type, $adapter_name, $namespace='Everon\DataMapper\Schema\Column\Validator');

    /**
     * @param string $name
     * @param array $data
     * @param string $class_name
     * @return Config\Interfaces\Item|Config\Item
     * @throws Exception\Factory
     */
    function buildConfigItem($name, array $data, $class_name='Everon\Config\Item');

    /**
     * @param Interfaces\Config $Config
     * @param Interfaces\RequestValidator $Validator
     * @param string $namespace
     * @return Interfaces\Router
     * @throws Exception\Factory
     */
    function buildRouter(Interfaces\Config $Config, Interfaces\RequestValidator $Validator, $namespace='Everon');

    /**
     * @param string $namespace
     * @return Interfaces\RequestValidator
     * @throws Exception\Factory
     */
    function buildRequestValidator($namespace='Everon\Http');

    /**
     * @param $root
     * @param string $namespace
     * @return Interfaces\FileSystem
     * @throws Exception\Factory
     */
    function buildFileSystem($root, $namespace='Everon\View');

    /**
     * @param $type
     * @param $cache_directory
     * @param string $namespace
     * @return \Everon\FileSystem\Interfaces\CacheLoader
     * @throws Exception\Factory
     */
    function buildFileSystemCacheLoader($type, $cache_directory, $namespace = 'Everon\FileSystem\CacheLoader');    

    /**
     * @param resource $handle default is tmpfile()
     * @param string $namespace
     * @return \Everon\FileSystem\Interfaces\TmpFile
     * @throws Exception\Factory
     */
    function buildFileSystemTmpFile($handle=null, $namespace='Everon\FileSystem');

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
     * @param string $namespace
     * @return View\Interfaces\Template
     * @throws Exception\Factory
     */
    function buildTemplate($filename, array $template_data, $namespace='Everon\View');

    /**
     * @param $template_string
     * @param array $template_data
     * @param string $namespace
     * @return View\Interfaces\TemplateContainer
     * @throws Exception\Factory
     */
    function buildTemplateContainer($template_string, array $template_data, $namespace='Everon\View\Template');

    /**
     * @param $class_name
     * @param string $namespace
     * @return View\Interfaces\TemplateCompiler
     * @throws Exception\Factory
     */
    function buildTemplateCompiler($class_name, $namespace='Everon\View\Template\Compiler');

    /**
     * @param string $namespace
     * @return View\Interfaces\TemplateCompilerContext
     * @throws Exception\Factory
     */
    function buildTemplateCompilerContext($namespace='Everon\View\Template\Compiler');

    /**
     * @param $class_name
     * @param $template_directory
     * @param $default_extension
     * @param string $namespace
     * @return View\Interfaces\View
     * @throws Exception\Factory
     */
    function buildView($class_name, $template_directory, $default_extension, $namespace='Everon\View');

    /**
     * @param Interfaces\FileSystem $FileSystem
     * @return View\Interfaces\Cache
     * @throws Exception\Factory
     */
    function buildViewCache(Interfaces\FileSystem $FileSystem);

    /**
     * @param Config\Interfaces\ItemRouter $RouteItem
     * @param string $namespace
     * @return View\Interfaces\Form
     * @throws Exception\Factory
     */
    function buildViewHtmlForm(Config\Interfaces\ItemRouter $RouteItem, $namespace='Everon\View\Html');

    /**
     * @param array $compilers_to_init
     * @param $view_directory
     * @param $cache_directory
     * @param string $namespace
     * @return View\Interfaces\Manager
     * @throws Exception\Factory
     */
    function buildViewManager(array $compilers_to_init, $view_directory, $cache_directory, $namespace='Everon\View');

    /**
     * @param string $name
     * @param View\Interfaces\View $View
     * @param string $namespace
     * @return View\Interfaces\Widget
     * @throws Exception\Factory
     */
    function buildViewWidget($name, View\Interfaces\View $View, $namespace='Everon\View');

    /**
     * @param \Everon\View\Interfaces\Manager $ViewManager
     * @param string $namespace
     * @return View\Interfaces\WidgetManager
     * @throws Exception\Factory
     */
    function buildViewWidgetManager(View\Interfaces\Manager $ViewManager, $namespace = 'Everon\View\Widget');

    /**
     * @param $directory
     * @param boolean $enabled
     * @param string $namespace
     * @return Interfaces\Logger
     * @throws Exception\Factory
     */
    function buildLogger($directory, $enabled, $namespace='Everon');

    /**
     * @param array $headers
     * @param string $namespace
     * @return Interfaces\Collection
     * @throws Exception\Factory
     */
    function buildHttpHeaderCollection(array $headers=[], $namespace='Everon\Http');

    /**
     * @param $guid
     * @param string $namespace
     * @return Interfaces\Response
     * @throws Exception\Factory
     */
    function buildResponse($guid, $namespace='Everon');


    /**
     * @param string $name
     * @param mixed $value if json string or array is used, the $use_json will be set to true
     * @param mixed $expire_date int as in 'time()' or string as in '+15 minutes'
     * @param string $namespace
     * @return Http\Interfaces\Cookie
     * @throws Exception\Factory
     */
    function buildHttpCookie($name, $value, $expire_date, $namespace='Everon\Http\Cookie');

    /**
     * @param array $data
     * @param string $namespace
     * @return Http\Interfaces\CookieCollection
     * @throws Exception\Factory
     */
    function buildHttpCookieCollection(array $data=[], $namespace='Everon\Http');

    /**
     * @param $guid
     * @param Http\Interfaces\HeaderCollection $HeaderCollection
     * @param Http\Interfaces\CookieCollection $CookieCollection
     * @param string $namespace
     * @return Http\Interfaces\Response
     * @throws Exception\Factory
     */
    function buildHttpResponse($guid, Http\Interfaces\HeaderCollection $HeaderCollection, Http\Interfaces\CookieCollection $CookieCollection, $namespace='Everon\Http');

    /**
     * @param $evrid
     * @param string $namespace
     * @return Http\Session
     * @throws Exception\Factory
     */
    function buildHttpSession($evrid, $namespace='Everon\Http');

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @param string $namespace
     * @return Interfaces\Request
     * @throws Exception\Factory
     */
    function buildConsoleRequest(array $server, array $get, array $post, array $files, $namespace='Everon\Console');

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @param string $namespace
     * @return Interfaces\Request
     * @throws Exception\Factory
     */
    function buildHttpRequest(array $server, array $get, array $post, array $files, $namespace='Everon\Http');

    /**
     * @param $app_root
     * @param $source_root
     * @param string $namespace
     * @return Interfaces\Environment
     * @throws Exception\Factory
     */
    function buildEnvironment($app_root, $source_root, $namespace='Everon');

    /**
     * @param string $namespace
     * @return \Everon\Event\Interfaces\Manager
     * @throws Exception\Factory
     */
    function buildEventManager($namespace='Everon\Event');

    /**
     * @param callable $Callback
     * @param string $namespace
     * @param mixed $Scope
     * @return \Everon\Event\Interfaces\Context
     * @throws Exception\Factory
     */
    function buildEventContext(\Closure $Callback, $Scope, $namespace='Everon\Event');

    /**
     * @param $name
     * @param Interfaces\Config $module_directory
     * @param Interfaces\Config $Config
     * @param string $namespace
     * @return \Everon\Module\Interfaces\Module
     * @throws Exception\Factory
     */
    function buildModule($name, $module_directory, Interfaces\Config $Config, $namespace='Everon\Module');

    /**
     * @param string $namespace
     * @return \Everon\Module\Interfaces\Manager
     * @throws Exception\Factory
     */
    function buildModuleManager($namespace='Everon\Module');

    /**
     * @param $url
     * @param array $supported_versions
     * @param $versioning
     * @param array $mapping
     * @param $namespace
     * @return Rest\Interfaces\ResourceManager
     * @throws Exception\Factory
     */
    function buildRestResourceManager($url, array $supported_versions, $versioning, array $mapping, $namespace='Everon\Rest\Resource');

    /**
     * @param $name
     * @param string $namespace
     * @return \Everon\Interfaces\FactoryWorker
     * @throws \Everon\Exception\Factory
     */
    function buildFactoryWorker($name, $namespace='Everon\Module');

    /**
     * @param $email
     * @param $name
     * @param string $namespace
     * @return Email\Interfaces\Address
     * @throws Exception\Factory
     */
    function buildEmailAddress($email, $name, $namespace='Everon\Email');

    /**
     * @param Email\Interfaces\Recipient $Recipient
     * @param Email\Interfaces\Address $FromAddress
     * @param string $subject
     * @param string $html_body
     * @param string $text_body
     * @param array $attachments
     * @param array $headers
     * @param string $namespace
     * @return Email\Interfaces\Message
     * @throws Exception\Factory
     */
    function buildEmailMessage(Email\Interfaces\Recipient $Recipient, Email\Interfaces\Address $FromAddress, $subject, $html_body, $text_body='', array $attachments = [], array $headers = [], $namespace = 'Everon\Email');

    /**
     * @param $name
     * @param Email\Interfaces\Credential $Credentials
     * @param string $namespace
     * @return Email\Interfaces\Sender
     * @throws Exception\Factory
     */
    function buildEmailSender($name, Email\Interfaces\Credential $Credentials, $namespace='Everon\Email');

    /**
     * @param array $to array of Email\Interfaces\Address
     * @param array $cc array of Email\Interfaces\Address
     * @param array $bcc array of Email\Interfaces\Address
     * @param string $namespace
     * @return \Everon\Email\Interfaces\Recipient
     * @throws Exception\Factory
     */
    function buildEmailRecipient(array $to, array $cc=[], array $bcc=[], $namespace='Everon\Email');

    /**
     * @param string $namespace
     * @return \Everon\Email\Interfaces\Manager
     * @throws Exception\Factory
     */
    function buildEmailManager($namespace='Everon\Email');

    /**
     * @param array $credential_data
     * @return Email\Credential
     * @throws Exception\Factory
     */
    function buildEmailCredential(array $credential_data);

    /**
     * @param string $namespace
     * @return \Everon\Task\Interfaces\Manager
     * @throws \Everon\Exception\Factory
     */
    function buildTaskManager($namespace='Everon\Task');

    /**
     * @param $type
     * @param mixed $data
     * @param string $namespace
     * @return \Everon\Task\Interfaces\Item
     * @throws \Everon\Exception\Factory
     */
    function buildTaskItem($type, $data, $namespace);

    /**
     * @param int $total
     * @param int $offset
     * @param int $limit
     * @param string $namespace
     * @return Paginator
     * @throws Exception\Factory
     */
    function buildPaginator($total, $offset, $limit, $namespace='Everon\Helper');

    /**
     * @param string $time
     * @param \DateTimeZone $timezone
     * @return \DateTime
     * @throws Exception\Factory
     */
    function buildDateTime($time='now', \DateTimeZone $timezone=null);

    /**
     * @param $timezone
     * @return \DateTimeZone
     * @throws Exception\Factory
     */
    function buildDateTimeZone($timezone);

    /**
     * @param $locale
     * @param $datetype
     * @param $timetype
     * @param $timezone
     * @param $calendar
     * @param $pattern
     * @return \IntlDateFormatter
     * @throws Exception\Factory
     */
    function buildIntlDateFormatter($locale, $datetype, $timetype, $timezone, $calendar, $pattern);

}
