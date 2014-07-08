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

use Everon\Email;
use Everon\Helper;
use Everon\Interfaces;

abstract class Factory implements Interfaces\Factory
{
    use Helper\String\UnderscoreToCamel;
    use Helper\Arrays;
    use Helper\Exceptions;
    use Helper\Asserts\IsStringAndNotEmpty;
    use Helper\Asserts\IsNumericAndNotZero;
    use Helper\Asserts\IsArrayKey;


    /**
     * @var Interfaces\DependencyContainer
     */
    protected $DependencyContainer = null;


    /**
     * @param $class_name
     * @param $Receiver
     */
    //abstract public function injectDependencies($class_name, $Receiver);

    /**
     * @inheritdoc
     */
    public function injectDependencies($class_name, $Receiver)
    {
        $this->getDependencyContainer()->inject($class_name, $Receiver);
        if ($this->getDependencyContainer()->wantsFactory($class_name)) {
            $Receiver->setFactory($this);
        }
    }

    /**
     * @var Interfaces\Collection
     */
    //protected $WorkerCollection = null;


    /**
     * @param Interfaces\DependencyContainer $Container
     */
    public function __construct(Interfaces\DependencyContainer $Container)
    {
        $this->DependencyContainer = $Container;
        //$this->WorkerCollection = new Helper\Collection([]);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception\Factory
     */
    /*
    public function __call($name, $arguments)
    {
        foreach ($this->WorkerCollection as $worker_name => $Worker) {
            d($worker_name, $Worker);
            / **
             * @var Interfaces\FactoryWorker $Worker
             * /
            if ($Worker->getMethods()->has($name)) {
                return call_user_func_array([$Worker, $name], $arguments);
            }
        }

        throw new Exception\Factory('Invalid factory method: "%s"', $name);
    }

    public function registerWorker(Interfaces\FactoryWorker $Worker)
    {
        $name = get_class($Worker);
        $this->WorkerCollection->set($name, $Worker);
        $Worker->register($this);
    }
    
    public function unRegisterWorker(Interfaces\FactoryWorker $Worker)
    {
        $name = get_class($Worker);
        $this->WorkerCollection->set($name, $Worker);
        $Worker->unRegister();
    }*/

    /**
     * @inheritdoc
     */
    public function getDependencyContainer()
    {
        return $this->DependencyContainer;
    }

    /**
     * @inheritdoc
     */
    public function setDependencyContainer(Interfaces\DependencyContainer $Container)
    {
        $this->DependencyContainer = $Container;
    }

    /**
     * @inheritdoc
     */
    public function getFullClassName($namespace, $class_name)
    {
        if ($class_name[0] === '\\') { //used for when laading classmap from cache
            return $class_name; //absolute name
        }

        $class = $namespace.'\\'.$class_name;
        return $class;
    }

    /**
     * @inheritdoc
     */
    public function classExists($class)
    {
        if (class_exists($class, true) === false) {
            throw new Exception\Factory('File for class: "%s" could not be found', $class);
        }
    }

    /**
     * @param $name
     * @param string $namespace
     * @return mixed
     * @throws Exception\Factory
     */
    protected function buildCore($name, $namespace='Everon')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $name);
            $this->classExists($class_name);
            $Core = new $class_name();
            $this->injectDependencies($class_name, $Core);
            return $Core;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Core: "%s\%s" initialization error', [$namespace, $name], $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConsole($namespace='Everon\Console')
    {
        return $this->buildCore('Core', $namespace);
    }

    /**
     * @inheritdoc
     */
    public function buildMvc($namespace='Everon\Mvc')
    {
        return $this->buildCore('Core', $namespace);
    }

    /**
     * @inheritdoc
     */
    public function buildRestCurlAdapter()
    {
        try {
            $Adapter = new Rest\CurlAdapter();
            $this->injectDependencies('Everon\Rest\CurlAdapter', $Adapter);
            return $Adapter;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RestClient initialization error', null, $e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function buildRestClient($Href, Rest\Interfaces\CurlAdapter $CurlAdapter)
    {
        try {
            $Client = new Rest\Client($Href, $CurlAdapter);
            $this->injectDependencies('Everon\Rest\Client', $Client);
            return $Client;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RestClient initialization error', null, $e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function buildRestServer($namespace='Everon\Rest')
    {
        return $this->buildCore('Server', $namespace);
    }

    /**
     * @inheritdoc
     */
    public function buildRestRequest(array $server, array $get, array $post, array $files, $versioning, $namespace='Everon\Rest')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Request');
            $this->classExists($class_name);
            $Request = new $class_name($server, $get, $post, $files, $versioning);
            $this->injectDependencies($class_name, $Request);
            return $Request;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RestRequest initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildRestResponse($guid, Http\Interfaces\HeaderCollection $HeaderCollection, Http\Interfaces\CookieCollection $CookieCollection, $namespace='Everon\Http')
    {
        try {
            $Response = new Rest\Response($guid, $HeaderCollection, $CookieCollection);
            $this->injectDependencies('Everon\Rest\Response', $Response);
            return $Response;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RestResponse initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildRestResource($name, $version, Rest\Interfaces\ResourceHref $Href, $resource_name, Domain\Interfaces\Entity $Entity, $namespace='Everon\Rest\Resource')
    {
        try {
            $class_name = $this->getFullClassName('Everon\Rest\Resource\\'.$name, $version);
            $this->classExists($class_name);
            $Resource = new $class_name($Href, $Entity);
            $this->injectDependencies($class_name, $Resource);
            return $Resource;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RestResource initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildRestCollectionResource($name, Rest\Interfaces\ResourceHref $Href, Interfaces\Collection $Collection, Interfaces\Paginator $Paginator, $namespace='Everon\Rest\Resource')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Collection');
            $this->classExists($class_name);
            $CollectionResource = new $class_name($Href, $Collection, $Paginator);
            $this->injectDependencies($class_name, $CollectionResource);
            return $CollectionResource;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RestCollectionResource initialization error', null, $e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function buildRestResourceManager($url, array $supported_versions, $versioning, array $mapping, $namespace='Everon\Rest\Resource')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Manager');
            $this->classExists($class_name);
            $Manager = new $class_name($url, $supported_versions, $versioning, $mapping);
            $this->injectDependencies($class_name, $Manager);
            return $Manager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RestResourceManager initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildRestResourceNavigator(Rest\Interfaces\Request $Request, $namespace='Everon\Rest\Resource')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Navigator');
            $this->classExists($class_name);
            $Navigator = new $class_name($Request);
            $this->injectDependencies($class_name, $Navigator);
            return $Navigator;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RestResourceNavigator initialization error', null, $e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function buildRestApiKey($id, $secret)
    {
        try {
            $ApiKey = new Rest\ApiKey($id, $secret);
            $this->injectDependencies('Everon\Rest\ApiKey', $ApiKey);
            return $ApiKey;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RestApiKey initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConfig($name, Config\Interfaces\LoaderItem $ConfigLoaderItem, \Closure $Compiler)
    {
        try {
            $ConfigFile = new \SplFileInfo($ConfigLoaderItem->getFilename());
            $class_name = $ConfigFile->getBasename('.ini');
            $class_name = ucfirst($this->stringUnderscoreToCamel($class_name));
            $class_name = $this->getFullClassName('Everon\Config', $class_name);
            
            try {
                if (class_exists($class_name, true) == false) {
                    $class_name = 'Everon\Config';
                }
            }
            catch (\Exception $e) {
                $class_name = 'Everon\Config';
            }

            $Config = new $class_name($name, $ConfigLoaderItem, $Compiler);
            $this->injectDependencies($class_name, $Config);
            return $Config;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Config: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConfigManager(Config\Interfaces\Loader $Loader)
    {
        try {
            $Manager = new Config\Manager($Loader);
            $this->injectDependencies('Everon\Config\Manager', $Manager);
            return $Manager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigManager initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConfigExpressionMatcher()
    {
        try {
            $ExpressionMatcher = new Config\ExpressionMatcher();
            $this->injectDependencies('Everon\Config\ExpressionMatcher', $ExpressionMatcher);
            return $ExpressionMatcher;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigExpressionMatcher initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConfigLoader($config_directory, $cache_directory)
    {
        try {
            $ConfigLoader = new Config\Loader($config_directory, $cache_directory);
            $this->injectDependencies('Everon\Config\Loader', $ConfigLoader);
            return $ConfigLoader;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigLoader initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConfigLoaderItem($filename, array $data)
    {
        try {
            $ConfigLoaderItem = new Config\Loader\Item($filename, $data);
            $this->injectDependencies('Everon\Config\Loader\Item', $ConfigLoaderItem);
            return $ConfigLoaderItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigLoaderItem initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildController($class_name, Interfaces\Module $Module, $namespace='Everon\Controller')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $class_name);
            $this->classExists($class_name);
            $Controller = new $class_name($Module);
            $this->injectDependencies($class_name, $Controller);
            return $Controller;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Controller: "%s" initialization error', $class_name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildView($class_name, $template_directory, $default_extension, $namespace='Everon\View')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $class_name);
            $this->classExists($class_name);
            /**
             * @var View\Interfaces\View $View
             */
            $View = new $class_name($template_directory, $default_extension);
            $this->injectDependencies($class_name, $View);
            return $View;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('View: "%s" initialization error', $class_name, $e);
        }
    }

    /**
     * @param Interfaces\FileSystem $FileSystem
     * @return View\Interfaces\Cache
     * @throws Exception\Factory
     */
    public function buildViewCache(Interfaces\FileSystem $FileSystem)
    {
        try {
            $ViewCache = new View\Cache($FileSystem);
            $this->injectDependencies('Everon\View\Cache', $ViewCache);
            return $ViewCache;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ViewCache initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildViewHtmlForm(Config\Interfaces\ItemRouter $RouteItem, $namespace='Everon\View\Html')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Form');
            $this->classExists($class_name);
            /**
             * @var View\Interfaces\View $ViewHtmlForm
             */
            $ViewHtmlForm = new $class_name($RouteItem);
            $this->injectDependencies($class_name, $ViewHtmlForm);
            return $ViewHtmlForm;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ViewHtmlForm initialization error for: "%s"', $RouteItem->getName(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildViewManager(array $compilers_to_init, $view_directory, $cache_directory)
    {
        try {
            $compilers = [];
            foreach ($compilers_to_init as $name => $extension) {
                $compilers[$extension][] = $this->buildTemplateCompiler($this->stringUnderscoreToCamel($name));
            }

            $Manager = new View\Manager($compilers, $view_directory, $cache_directory);
            $this->injectDependencies('Everon\View\Manager', $Manager);
            return $Manager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ViewManager initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildViewWidget($name, View\Interfaces\View $View, $namespace='Everon\View')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $name);
            $this->classExists($class_name);
            /**
             * @var View\Interfaces\Widget $Widget
             */
            $Widget = new $class_name($View);
            $this->injectDependencies($class_name, $Widget);
            return $Widget;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ViewWidget: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildTemplateCompiler($class_name, $namespace='Everon\View\Template\Compiler')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $class_name);
            $this->classExists($class_name);
            $Compiler = new $class_name();
            $this->injectDependencies($class_name, $Compiler);
            return $Compiler;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('TemplateCompiler: "%s" initialization error', $class_name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildPdo($dsn, $username, $password, $options)
    {
        try {
            $options = $this->arrayMergeDefault([
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ], $options);
            return new \PDO($dsn, $username, $password, $options);
        }
        catch (\Exception $e) {
            throw new Exception\Factory('PDO initialization error', null, $e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function buildPdoAdapter(\PDO $Pdo, DataMapper\Interfaces\ConnectionItem $ConnectionItem)
    {
        try {
            $PdoAdapter = new PdoAdapter($Pdo, $ConnectionItem);
            $this->injectDependencies('Everon\PdoAdapter', $PdoAdapter);
            return $PdoAdapter;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('PdoAdapter initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConnectionItem(array $data, $namespace='Everon\DataMapper')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Connection\Item');
            $this->classExists($class_name);
            $Item = new $class_name($data);
            $this->injectDependencies($class_name, $Item);
            return $Item;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConnectionItem initialization error', null, $e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function buildConnectionManager(Interfaces\Config $DatabaseConfig, $namespace='Everon\DataMapper')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Connection\Manager');
            $this->classExists($class_name);

            $connections = [];
            $data = $DatabaseConfig->toArray(true);
            foreach ($data as $name => $item_data) {
                $Item = $this->buildConnectionItem($item_data);
                $connections[$name] = $Item;
            }

            $ConnectionManager = new $class_name($connections);
            $this->injectDependencies($class_name, $ConnectionManager);
            return $ConnectionManager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConnectionManager initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildDataMapper($name, DataMapper\Interfaces\Schema\Table $Table, DataMapper\Interfaces\Schema $Schema, $namespace='Everon\DataMapper')
    {
        try {
            $adapter_name = $Schema->getAdapterName();
            $class_name = $this->getFullClassName($namespace, $adapter_name.'\\'.$name);
            $this->classExists($class_name);
            $DataMapper = new $class_name($Table, $Schema);
            $this->injectDependencies($class_name, $DataMapper);
            return $DataMapper;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('DataMapper: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildDataMapperManager(DataMapper\Interfaces\ConnectionManager $ConnectionManager, Domain\Interfaces\Mapper $DomainMapper, $namespace='Everon\DataMapper')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Manager');
            $this->classExists($class_name);
            $DataMapperManager = new $class_name($ConnectionManager, $DomainMapper);
            $this->injectDependencies($class_name, $DataMapperManager);
            return $DataMapperManager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('DataMapperManager initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildDomainEntity($class_name, $id_field, array $data, $namespace='Everon\Domain')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $class_name.'\Entity');
            $this->classExists($class_name);
            $Entity = new $class_name($id_field, $data);
            $this->injectDependencies($class_name, $Entity);
            return $Entity;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('DomainEntity: "%s" initialization error', $class_name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildDomainRepository($name, Interfaces\DataMapper $DataMapper, $namespace='Everon\Domain')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $name.'\Repository');
            $this->classExists($class_name);
            $Repository = new $class_name($name, $DataMapper);
            $this->injectDependencies($class_name, $Repository);
            return $Repository;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('DomainRepository: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildDomainMapper(array $mappings, $namespace='Everon\Domain')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Mapper');
            $this->classExists($class_name);
            $DomainManager = new $class_name($mappings);
            $this->injectDependencies($class_name, $DomainManager);
            return $DomainManager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('DomainMapper initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildDomainManager(DataMapper\Interfaces\Manager $Manager, $namespace='Everon\Domain')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Manager');
            $this->classExists($class_name);
            $DomainManager = new $class_name($Manager);
            $this->injectDependencies($class_name, $DomainManager);
            return $DomainManager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('DomainManager initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildDomainModel($class_name, $namespace='Everon\Domain')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $class_name.'\Model');
            $this->classExists($class_name);
            $Model = new $class_name();
            $this->injectDependencies($class_name, $Model);
            return $Model;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('DomainModel: "%s" initialization error', $class_name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildSchema(DataMapper\Interfaces\Schema\Reader $Reader, DataMapper\Interfaces\Connectionmanager $ConnectionManager, Domain\Interfaces\Mapper $DomainMapper, $namespace='Everon\DataMapper')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Schema');
            $this->classExists($class_name);
            $Schema = new $class_name($Reader, $ConnectionManager, $DomainMapper);
            $this->injectDependencies($class_name, $Schema);
            return $Schema;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Schema initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildSchemaReader(Interfaces\PdoAdapter $PdoAdapter, $namespace='Everon\DataMapper\Schema')
    {
        try {
            $ConnectionItem = $PdoAdapter->getConnectionConfig();
            $class_name = $ConnectionItem->getAdapterName().'\Reader';
            $class_name = $this->getFullClassName($namespace, $class_name);
            $this->classExists($class_name);

            $SchemaReader = new $class_name($PdoAdapter);
            $this->injectDependencies($class_name, $SchemaReader);
            return $SchemaReader;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('SchemaReader initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildSchemaTableAndDependencies($name, $schema, $adapter_name, array $columns, array $primary_keys,  array $unique_keys, array $foreign_keys, Domain\Interfaces\Mapper $DomainMapper, $namespace='Everon\DataMapper')
    {
        try {
            $primary_key_list = [];
            foreach ($primary_keys as $constraint_data) {
                /**
                 * @var \Everon\DataMapper\Interfaces\Schema\PrimaryKey $PrimaryKey
                 */
                $class_name = $this->getFullClassName($namespace, 'Schema\PrimaryKey');
                $this->classExists($class_name);
                $PrimaryKey = new $class_name($constraint_data);
                $primary_key_list[$PrimaryKey->getName()] = $PrimaryKey;
            }

            $unique_key_list = [];
            foreach ($unique_keys as $constraint_data) {
                /**
                 * @var \Everon\DataMapper\Interfaces\Schema\UniqueKey $UniqueKey
                 */
                $class_name = $this->getFullClassName($namespace, 'Schema\UniqueKey');
                $this->classExists($class_name);
                $UniqueKey = new $class_name($constraint_data);
                $unique_key_list[$UniqueKey->getName()] = $UniqueKey;
            }
            
            $foreign_key_list = [];
            foreach ($foreign_keys as $foreign_key_data) {
                /**
                 * @var \Everon\DataMapper\Interfaces\Schema\ForeignKey $ForeignKey
                 */
                $class_name = $this->getFullClassName($namespace, 'Schema\ForeignKey');
                $this->classExists($class_name);
                $ForeignKey = new $class_name($foreign_key_data);
                $foreign_key_list[$ForeignKey->getName()] = new $class_name($foreign_key_data);
            }

            $column_list = [];
            foreach ($columns as $column_data) {
                $class_name = $this->getFullClassName($namespace, "Schema\\${adapter_name}\\Column");
                $this->classExists($class_name);
                /**
                 * @var DataMapper\Schema\Column $Column
                 */
                $Column = new $class_name($column_data, $primary_key_list, $unique_key_list, $foreign_key_list);
                $column_list[$Column->getName()] = $Column;
            }
            
            return $this->buildSchemaTable($name, $schema, $column_list, $primary_key_list, $unique_key_list, $foreign_key_list, $DomainMapper);
        }
        catch (\Exception $e) {
            throw new Exception\Factory('SchemaTableAndDependencies: "%s.%s" initialization error', [$schema,$name], $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildSchemaTable($name, $schema, array $column_list, array $primary_key_list,  array $unique_key_list, array $foreign_key_list, Domain\Interfaces\Mapper $DomainMapper, $namespace='Everon\DataMapper')
    {
        try {
            $class_name = $this->getFullClassName($namespace,'Schema\Table');
            $this->classExists($class_name);
            $Table = new $class_name($name, $schema, $column_list, $primary_key_list, $unique_key_list, $foreign_key_list, $DomainMapper);
            return $Table;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('SchemaTable: "%s.%s" initialization error', [$schema,$name], $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildSchemaConstraint(array $data, $namespace='Everon\DataMapper')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Schema\Constraint');
            $this->classExists($class_name);
            $constraint_list[] = $class_name($data);
            $Constraint = new $class_name($data);
            return $Constraint;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('SchemaConstraint initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildResponse($guid, $namespace='Everon')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Response');
            $this->classExists($class_name);
            $Response = new $class_name($guid);
            $this->injectDependencies($class_name, $Response);
            return $Response;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Response initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildHttpCookie($name, $value, $expire_date, $namespace='Everon\Http')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Cookie');
            $this->classExists($class_name);
            $Cookie = new $class_name($name, $value, $expire_date);
            $this->injectDependencies($class_name, $Cookie);
            return $Cookie;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('HttpCookie initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildHttpCookieCollection(array $data=[], $namespace='Everon\Http')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'CookieCollection');
            $this->classExists($class_name);
            
            $cookies = [];
            foreach ($data as $cookie_name => $cookie_value) {
                $Cookie = $this->buildHttpCookie($cookie_name, $cookie_value, 0);
                $cookies[$cookie_name] = $Cookie;
            }
            
            $CookieCollection = new $class_name($cookies);
            $this->injectDependencies($class_name, $CookieCollection);
            return $CookieCollection;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('HttpCookieCollection initialization error', null, $e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function buildHttpResponse($guid, Http\Interfaces\HeaderCollection $HeaderCollection, Http\Interfaces\CookieCollection $CookieCollection, $namespace='Everon\Http')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Response');
            $this->classExists($class_name);
            $Response = new $class_name($guid, $HeaderCollection, $CookieCollection);
            $this->injectDependencies($class_name, $Response);
            return $Response;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('HttpResponse initialization error', null, $e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function buildHttpSession($evrid, $namespace='Everon\Http')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Session');
            $this->classExists($class_name);
            $Session = new $class_name($evrid);
            $this->injectDependencies($class_name, $Session);
            return $Session;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('HttpSession initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildRouter(Interfaces\Config $Config, Interfaces\RequestValidator $Validator, $namespace='Everon')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Router');
            $this->classExists($class_name);
            $RouteItem = new $class_name($Config, $Validator);
            $this->injectDependencies($class_name, $RouteItem);
            return $RouteItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Router initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildRequestValidator($namespace='Everon')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'RequestValidator');
            $this->classExists($class_name);
            $RequestValidator = new $class_name();
            $this->injectDependencies($class_name, $RequestValidator);
            return $RequestValidator;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RequestValidator initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConfigItem($name, array $data, $class_name='Everon\Config\Item')
    {
        try {
            $this->classExists($class_name);
            $data[Config\Item::PROPERTY_NAME] = $name;
            $ConfigItem = new $class_name($data);
            $this->injectDependencies($class_name, $ConfigItem);
            return $ConfigItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigItem: "%s[%s]" initialization error', [$class_name, $name], $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildTemplate($filename, array $template_data, $namespace='Everon\View')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Template');
            $this->classExists($class_name);
            $Template = new $class_name($filename, $template_data);
            $this->injectDependencies($class_name, $Template);
            return $Template;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ViewTemplate initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildTemplateContainer($template_string, array $template_data, $namespace='Everon\View\Template')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Container');
            $this->classExists($class_name);
            $Container = new $class_name($template_string, $template_data);
            $this->injectDependencies($class_name, $Container);
            return $Container;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('TemplateContainer initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildFileSystem($root, $namespace='Everon')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'FileSystem');
            $this->classExists($class_name);
            $FileSystem = new $class_name($root);
            $this->injectDependencies($class_name, $FileSystem);
            return $FileSystem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('FileSystem initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildLogger($directory, $enabled, $namespace='Everon')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Logger');
            $this->classExists($class_name);
            $Logger = new $class_name($directory, $enabled);
            $this->injectDependencies($class_name, $Logger);
            return $Logger;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Logger initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildHttpHeaderCollection(array $headers=[], $namespace='Everon\Http')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'HeaderCollection');
            $this->classExists($class_name);
            $HeaderCollection = new $class_name($headers);
            $this->injectDependencies($class_name, $HeaderCollection);
            return $HeaderCollection;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('HttpHeaderCollection initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildHttpRequest(array $server, array $get, array $post, array $files, $namespace='Everon\Http')
    {
        try {
            return $this->buildRequest($server, $get, $post, $files, $namespace);
        }
        catch (\Exception $e) {
            throw new Exception\Factory('HttpRequest initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConsoleRequest(array $server, array $get, array $post, array $files, $namespace='Everon\Console')
    {
        try {
            return $this->buildRequest($server, $get, $post, $files, $namespace);
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConsoleRequest initialization error', null, $e);
        }
    }

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @param $namespace
     * @return Interfaces\Request
     * @throws Exception\Factory
     */
    protected function buildRequest(array $server, array $get, array $post, array $files, $namespace)
    {
        $class_name = $this->getFullClassName($namespace, 'Request');
        $this->classExists($class_name);
        $Request = new $class_name($server, $get, $post, $files);
        $this->injectDependencies($class_name, $Request);
        return $Request;
    }

    /**
     * @inheritdoc
     */
    public function buildEnvironment($app_root, $source_root, $namespace='Everon')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Environment');
            $this->classExists($class_name);
            $Environment = new $class_name($app_root, $source_root);
            $this->injectDependencies($class_name, $Environment);
            return $Environment;            
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Environment initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildEventManager($namespace='Everon\Event')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Manager');
            $this->classExists($class_name);
            $Manager = new $class_name();
            $this->injectDependencies($class_name, $Manager);
            return $Manager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('EventManager initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildEventContext(\Closure $Callback, $Scope, $namespace='Everon\Event')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Context');
            $this->classExists($class_name);
            $Scope = new $class_name($Callback, $Scope);
            $this->injectDependencies($class_name, $Scope);
            return $Scope;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('EventContext initialization error', null, $e);
        }
    }    
    
    /**
     * @inheritdoc
     */
    public function buildModule($name, $module_directory, Interfaces\Config $Config, $namespace='Everon\Module')
    {
        try {
            $class_name = $this->getFullClassName($namespace.'\\'.$name, 'Module');
            $this->classExists($class_name);
            $Module = new $class_name($name, $module_directory, $Config);
            $this->injectDependencies($class_name, $Module);
            return $Module;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Module: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildModuleManager($namespace='Everon\Module')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Manager');
            $this->classExists($class_name);
            $Manager = new $class_name();
            $this->injectDependencies($class_name, $Manager);
            return $Manager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ModuleManager initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildFactoryWorker($name, $namespace='Everon\Module')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $name.'\FactoryWorker');
            $this->classExists($class_name);
            $Worker = new $class_name($this);
            $this->injectDependencies($class_name, $Worker);
            return $Worker;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('FactoryWorker: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildEmailAddress($email, $name, $namespace='Everon\Email')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Address');
            $this->classExists($class_name);
            $Recipient = new $class_name($email, $name);
            $this->injectDependencies($class_name, $Recipient);
            return $Recipient;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('EmailAddress initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildEmailCredential(array $credential_data, $namespace='Everon\Email')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Credential');
            $this->classExists($class_name);

            //todo: move it to Credential or Manager, no validation in Factory
            $this->assertIsArrayKey('username', $credential_data);
            $this->assertIsArrayKey('password', $credential_data);
            $this->assertIsArrayKey('host', $credential_data);
            $this->assertIsArrayKey('port', $credential_data);
            $this->assertIsArrayKey('name', $credential_data);
            $this->assertIsArrayKey('encryption', $credential_data);

            $this->assertIsStringAndNonEmpty($credential_data['username']);
            $this->assertIsStringAndNonEmpty($credential_data['password']);
            $this->assertIsStringAndNonEmpty($credential_data['host']);
            $this->assertIsNumericAndNonZero($credential_data['port']);
            $this->assertIsStringAndNonEmpty($credential_data['name']);
            $this->assertIsStringAndNonEmpty($credential_data['encryption']);

            $Credentials = new Email\Credential();
            $Credentials->setUserName($credential_data['username']);
            $Credentials->setPassword($credential_data['password']);
            $Credentials->setHost($credential_data['host']);
            $Credentials->setPort($credential_data['port']);
            $Credentials->setName($credential_data['name']);
            $Credentials->setEncryption($credential_data['encryption']);

            return $Credentials;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('EmailCredential initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildEmailManager($namespace='Everon\Email')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Manager');
            $this->classExists($class_name);
            $EmailManager = new $class_name();
            $this->injectDependencies($class_name, $EmailManager);
            return $EmailManager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('EmailManager initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildEmailMessage(Email\Interfaces\Recipient $Recipient, Email\Interfaces\Address $FromAddress, $subject, $html_body, $text_body='', array $attachments = [], array $headers = [], $namespace = 'Everon\Email')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Message');
            $this->classExists($class_name);
            $Message = new $class_name($Recipient, $FromAddress, $subject, $html_body, $text_body, $attachments, $headers);
            $this->injectDependencies($class_name, $Message);
            return $Message;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('EmailMessage: "%s" initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildEmailRecipient(array $to, array $cc=[], array $bcc=[], $namespace='Everon\Email')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Recipient');
            $this->classExists($class_name);
            $Recipient = new $class_name($to, $cc, $bcc);
            $this->injectDependencies($class_name, $Recipient);
            return $Recipient;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('EmailRecipient initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildEmailSender($name, Email\Interfaces\Credential $Credential, $namespace='Everon\Email\Sender')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $name);
            $this->classExists($class_name);
            $Sender = new $class_name($Credential);
            $this->injectDependencies($class_name, $Sender);
            return $Sender;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('EmailSender: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildTaskItem($type, $data, $namespace)
    {
        try {
            $task_type = ucfirst($this->stringUnderscoreToCamel(strtolower($type)));
            $class_name = $this->getFullClassName($namespace, $task_type);
            $this->classExists($class_name);
            $Item = new $class_name($data);
            $Item->setType($type);
            $this->injectDependencies($class_name, $Item);
            return $Item;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('TaskItem: "%s" initialization error', $type, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildTaskManager($namespace='Everon\Task')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Manager');
            $this->classExists($class_name);
            $TaskHandler = new $class_name($this);
            $this->injectDependencies($class_name, $TaskHandler);
            return $TaskHandler;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('TaskManager initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildPaginator($total, $offset, $limit, $namespace = 'Everon\Helper')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Paginator');
            $this->classExists($class_name);
            $Paginator = new $class_name($total, $offset, $limit);
            $this->injectDependencies($class_name, $Paginator);
            return $Paginator;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Paginator initialization error', null, $e);
        }
    }
}