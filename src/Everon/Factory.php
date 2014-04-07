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

use Everon\Helper;
use Everon\Interfaces;

abstract class Factory implements Interfaces\Factory
{
    use Helper\String\UnderscoreToCamel;
    use Helper\Arrays;

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
     * @inheritdoc
     */
    public function buildConsole()
    {
        try {
            $Core = new Console();
            $this->injectDependencies('Everon\Console', $Core);
            return $Core;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Console initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildMvc()
    {
        try {
            $Core = new Mvc();
            $this->injectDependencies('Everon\Mvc', $Core);
            return $Core;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Mvc initialization error', null, $e);
        }
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
    public function buildRestServer()
    {
        try {
            $Server = new Rest\Server();
            $this->injectDependencies('Everon\Rest\Server', $Server);
            return $Server;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RestServer initialization error', null, $e);
        }
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
    public function buildRestResponse($guid, Http\Interfaces\HeaderCollection $Headers)
    {
        try {
            $Response = new Rest\Response($guid, $Headers);
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
    public function buildRestCollectionResource($name, Rest\Interfaces\ResourceHref $Href, Interfaces\Collection $Collection, $namespace='Everon\Rest\Resource')
    {
        try {
            $class_name = 'Everon\Rest\Resource\Collection';
            $this->classExists($class_name);
            $CollectionResource = new $class_name($Href, $Collection);
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
             * @var Interfaces\View $View
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
     * @return View\Cache
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
     * @param $class_name
     * @param string $namespace
     * @return Interfaces\TemplateCompiler
     * @throws Exception\Factory
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
    public function buildResponse($guid)
    {
        try {
            $Response = new Response($guid);
            $this->injectDependencies('Everon\Response', $Response);
            return $Response;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Response initialization error', null, $e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function buildHttpResponse($guid, Http\Interfaces\HeaderCollection $Headers)
    {
        try {
            $Response = new Http\Response($guid, $Headers);
            $this->injectDependencies('Everon\Http\Response', $Response);
            return $Response;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('HttpResponse initialization error', null, $e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function buildHttpSession($evrid)
    {
        try {
            $Session = new Http\Session($evrid);
            $this->injectDependencies('Everon\Http\Session', $Session);
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
    public function buildRequestValidator()
    {
        try {
            $RequestValidator = new RequestValidator();
            $this->injectDependencies('Everon\RequestValidator', $RequestValidator);
            return $RequestValidator;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RequestValidator initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConfigItem($name, array $data)
    {
        try {
            $data[Config\Item::PROPERTY_NAME] = $name;
            $ConfigItem = new Config\Item($data);
            $this->injectDependencies('Everon\Config\Item', $ConfigItem);
            return $ConfigItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigItem: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConfigItemDomain($name, array $data)
    {
        try {
            $data[Config\Item::PROPERTY_NAME] = $name;
            $DomainItem = new Config\Item\Domain($data);
            $this->injectDependencies('Everon\Config\Item\Domain', $DomainItem);
            return $DomainItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigItemDomain: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildConfigItemRouter($name, array $data)
    {
        try {
            $data[Config\Item::PROPERTY_NAME] = $name;
            $RouteItem = new Config\Item\Router($data);
            $this->injectDependencies('Everon\Config\Item\Router', $RouteItem);
            return $RouteItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigItemRouter: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildTemplate($filename, array $template_data)
    {
        try {
            $Template = new View\Template($filename, $template_data);
            $this->injectDependencies('Everon\View\Template', $Template);
            return $Template;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Template initialization error', null, $e);
        }
    }


    /**
     * @inheritdoc
     */
    public function buildFileSystem($root)
    {
        try {
            $FileSystem = new FileSystem($root);
            $this->injectDependencies('Everon\FileSystem', $FileSystem);
            return $FileSystem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('FileSystem initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildTemplateContainer($template_string, array $template_data)
    {
        try {
            $TemplateContainer = new View\Template\Container($template_string, $template_data);
            $this->injectDependencies('Everon\View\Template\Container', $TemplateContainer);
            return $TemplateContainer;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('TemplateContainer initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildLogger($directory, $enabled)
    {
        try {
            $Logger = new Logger($directory, $enabled);
            $this->injectDependencies('Everon\Logger', $Logger);
            return $Logger;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Logger initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildHttpHeaderCollection(array $headers=[])
    {
        try {
            /**
             * @var Interfaces\Collection $Headers
             */
            $Headers = new Http\HeaderCollection($headers);
            $this->injectDependencies('Everon\Http\HeaderCollection', $Headers);
            return $Headers;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('HttpHeaderCollection initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildRequest(array $server, array $get, array $post, array $files, $namespace='Everon')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Request');
            $this->classExists($class_name);
            $Request = new $class_name($server, $get, $post, $files);
            $this->injectDependencies($class_name, $Request);
            return $Request;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Request initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildEnvironment($app_root, $source_root)
    {
        try {
            $Environment = new Environment($app_root, $source_root);
            $this->injectDependencies('Everon\Environment', $Environment);
            return $Environment;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Environment initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildModule($name, $module_directory, Interfaces\Config $Config, Interfaces\Config $RouterConfig)
    {
        try {
            $class_name = $this->getFullClassName('Everon\Module\\'.$name, 'Module');
            $this->classExists($class_name);
            $Module = new $class_name($name, $module_directory, $Config, $RouterConfig);
            $this->injectDependencies($class_name, $Module);
            return $Module;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Module initialization error', null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildModuleManager()
    {
        try {
            $ModuleManager = new Module\Manager();
            $this->injectDependencies('Everon\Module\Manager', $ModuleManager);
            return $ModuleManager;
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
}