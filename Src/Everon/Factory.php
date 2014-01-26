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

use Everon\DataMapper\Interfaces\ConnectionManager;
use Everon\DataMapper\Schema;
use Everon\Helper;
use Everon\Interfaces;

class Factory implements Interfaces\Factory
{
    use Helper\String\UnderscoreToCamel;

    /**
     * @var Interfaces\DependencyContainer
     */
    protected $DependencyContainer = null;


    public function __construct(Interfaces\DependencyContainer $Container)
    {
        $this->DependencyContainer = $Container;
    }

    /**
     * @return Interfaces\DependencyContainer
     */
    public function getDependencyContainer()
    {
        return $this->DependencyContainer;
    }

    /**
     * @param Interfaces\DependencyContainer $Container
     */
    public function setDependencyContainer(Interfaces\DependencyContainer $Container)
    {
        $this->DependencyContainer = $Container;
    }

    /**
     * @param $class_name
     * @param $Receiver
     */
    public function injectDependencies($class_name, $Receiver)
    {
        $this->getDependencyContainer()->inject($class_name, $Receiver);
        if ($this->getDependencyContainer()->wantsFactory($class_name)) {
            $Receiver->setFactory($this);
        }
    }

    /**
     * @param $namespace
     * @param $class_name
     * @return string
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
     * @return Interfaces\Core
     * @throws Exception\Factory
     */
    public function buildConsole()
    {
        try {
            $Core = new Console();
            $this->injectDependencies('Everon\Core', $Core);
            return $Core;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Console initialization error', null, $e);
        }
    }

    /**
     * @return Interfaces\Core
     * @throws Exception\Factory
     */
    public function buildMvc()
    {
        try {
            $Core = new Mvc();
            $this->injectDependencies('Everon\Core', $Core);
            return $Core;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Mvc initialization error', null, $e);
        }
    }

    /**
     * @param $name
     * @param Interfaces\ConfigLoaderItem $ConfigLoaderItem
     * @param callable $Compiler
     * @return Interfaces\Config
     * @throws Exception\Factory
     */
    public function buildConfig($name, Interfaces\ConfigLoaderItem $ConfigLoaderItem, \Closure $Compiler)
    {
        try {
            $class_name = ucfirst($this->stringUnderscoreToCamel($name));
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
     * @param Interfaces\ConfigLoader $Loader
     * @return Interfaces\ConfigManager
     * @throws Exception\Factory
     */
    public function buildConfigManager(Interfaces\ConfigLoader $Loader)
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
     * @return Interfaces\ConfigExpressionMatcher
     * @throws Exception\Factory
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
     * @param $config_directory
     * @param $cache_directory
     * @return Config\Loader
     * @throws Exception\Factory
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
     * @param $filename
     * @param array $data
     * @return Config\Loader\Item
     * @throws Exception\Factory
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
     * @param $class_name
     * @param string $namespace
     * @return Interfaces\Controller
     * @throws Exception\Factory
     */
    public function buildController($class_name, $namespace='Everon\Controller')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $class_name);
            $Controller = new $class_name();
            $this->injectDependencies($class_name, $Controller);
            return $Controller;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Controller: "%s" initialization error', $class_name, $e);
        }
    }


    /**
     * @param $class_name
     * @param k
     * @param array $variables
     * @param string $namespace
     * @return Interfaces\View
     * @throws Exception\Factory
     */
    public function buildView($class_name, $template_directory, array $variables, $namespace='Everon\View')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $class_name);
            $View = new $class_name($template_directory, $variables);
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
     * @param array $compilers_to_init
     * @param $view_directory
     * @return Interfaces\ViewManager
     * @throws Exception\Factory
     */
    public function buildViewManager(array $compilers_to_init, $view_directory)
    {
        try {
            $compilers = [];
            foreach ($compilers_to_init as $name => $extension) {
                $compilers[$extension][] = $this->buildTemplateCompiler($this->stringUnderscoreToCamel($name));
            }

            $Manager = new View\Manager($compilers, $view_directory);
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
            $Pdo = new \PDO($dsn, $username, $password, $options);
            return $Pdo;
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

            $connections = [];
            $data = $DatabaseConfig->toArray();
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
    public function buildDataMapper(DataMapper\Interfaces\Schema\Table $Table, DataMapper\Interfaces\Schema $Schema, $namespace='Everon\DataMapper')
    {
        $name = $this->stringUnderscoreToCamel($Table->getName());
        try {
            $class_name = $this->getFullClassName($namespace, $name);
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
    public function buildDomainEntity($class_name, $id, array $data, $namespace='Everon\Test')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $class_name.'\Entity');
            $Entity = new $class_name($id, $data);
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
    public function buildDomainModel($class_name, $namespace='Everon\Domain')
    {
        try {
            $class_name = $this->getFullClassName($namespace, $class_name.'\Model');
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
    public function buildDomainManager(ConnectionManager $ConnectionManager, $namespace='Everon\Domain')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Manager');
            $DomainManager = new $class_name($ConnectionManager);
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
    public function buildSchema(DataMapper\Interfaces\Schema\Reader $Reader, DataMapper\Interfaces\Connectionmanager $ConnectionManager, $namespace='Everon\DataMapper')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Schema');
            $Schema = new $class_name($Reader, $ConnectionManager);
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
    public function buildSchemaReader(DataMapper\Interfaces\ConnectionItem $ConnectionItem, Interfaces\PdoAdapter $PdoAdapter, $namespace='Everon\dataMapper\Schema')
    {
        try {
            $class_name = $ConnectionItem->getAdapterName().'\Reader';
            $class_name = $this->getFullClassName($namespace, $class_name);

            $SchemaReader = new $class_name($ConnectionItem->getName(), $PdoAdapter);
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
    public function buildSchemaTable($name, array $columns, array $constraints, array $foreign_keys, $namespace='Everon\DataMapper')
    {
        try {
            $column_list = [];
            foreach ($columns as $column_data) {
                $class_name = $this->getFullClassName($namespace, 'Schema\MySql\Column');
                $column_list[] = $class_name($column_data); //todo: coupled to mysql
            }

            $constraint_list = [];
            foreach ($constraints as $constraint_data) {
                $class_name = $this->getFullClassName($namespace, 'Schema\Constraint');
                $constraint_list[] = $class_name($constraint_data);
            }

            $fk_list = [];
            foreach ($foreign_keys as $fk_data) {
                $class_name = $this->getFullClassName($namespace, 'Schema\ForeignKey');
                $fk_list[] = $class_name($fk_data);
            }

            $class_name = $this->getFullClassName($namespace,'Schema\Table');
            $Table = new $class_name($name, $column_list, $constraint_list, $fk_list);
            return $Table;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('SchemaTable: "%" initialization error', $name, $e);
        }
    } 
    
    /**
     * @inheritdoc
     */
    public function buildSchemaConstraint(array $data, $namespace='Everon\DataMapper')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Schema\Constraint');
            $constraint_list[] = $class_name($data);
            $Constraint = new $class_name($data);
            return $Constraint;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('SchemaConstraint initialization error', null, $e);
        }
    }

    /**
     * @param Interfaces\Collection $Headers
     * @return Interfaces\Response
     * @throws Exception\Factory
     */
    public function buildResponse(Interfaces\Collection $Headers)
    {
        try {
            $Response = new Response($Headers);
            $this->injectDependencies('Everon\Response', $Response);
            return $Response;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Response initialization error', null, $e);
        }
    }

    /**
     * @param Interfaces\Config $Config
     * @param Interfaces\RequestValidator $Validator
     * @param string $namespace
     * @return Interfaces\Router|Router
     * @throws Exception\Factory
     */
    public function buildRouter(Interfaces\Config $Config, Interfaces\RequestValidator $Validator, $namespace='Everon')
    {
        try {
            $class_name = $this->getFullClassName($namespace, 'Router');
            $RouteItem = new $class_name($Config, $Validator);
            $this->injectDependencies($class_name, $RouteItem);
            return $RouteItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Router initialization error', null, $e);
        }
    }

    /**
     * @return Interfaces\RequestValidator
     * @throws Exception\Factory
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
     * @param $name
     * @param array $data
     * @return Interfaces\ConfigItem
     * @throws Exception\Factory
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
            throw new Exception\Factory('ConfigItemRouter: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @param $name
     * @param array $data
     * @return Interfaces\ConfigItemRouter
     * @throws Exception\Factory
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
     * @param $filename
     * @param array $template_data
     * @return Interfaces\TemplateContainer
     * @throws Exception\Factory
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
     * @param $root
     * @return FileSystem
     * @throws Exception\Factory
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
     * @param $template_string
     * @param array $template_data
     * @return Interfaces\TemplateContainer
     * @throws Exception\Factory
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
     * @param $directory
     * @param boolean $enabled
     * @return Interfaces\Logger|Logger
     * @throws Exception\Factory
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
     * @param array $headers
     * @return Interfaces\Collection
     * @throws Exception\Factory
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
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @return Interfaces\Request
     * @throws Exception\Factory
     */
    public function buildRequest(array $server, array $get, array $post, array $files)
    {
        try {
            $Request = new Request($server, $get, $post, $files);
            $this->injectDependencies('Everon\Request', $Request);
            return $Request;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Request initialization error', null, $e);
        }
    }

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @return Interfaces\Request
     * @throws Exception\Factory
     */
    public function buildConsoleRequest(array $server, array $get, array $post, array $files)
    {
        try {
            $Request = new Console\Request($server, $get, $post, $files);
            $this->injectDependencies('Everon\Console\Request', $Request);
            return $Request;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Console Request initialization error', null, $e);
        }
    }

    /**
     * @param $root
     * @return Interfaces\Environment
     * @throws Exception\Factory
     */
    public function buildEnvironment($root)
    {
        try {
            $Environment = new Environment($root);
            $this->injectDependencies('Everon\Environment', $Environment);
            return $Environment;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Environment initialization error', null, $e);
        }
    }
}