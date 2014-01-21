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
use Everon\DataMapper\Interfaces\Schema as SchemaInterface;
use Everon\DataMapper\Schema;

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
        if ($class_name[0] === '\\') {
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
            throw new Exception\Factory('Core initialization error', null, $e);
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
            throw new Exception\Factory('Core initialization error', null, $e);
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
     * @return Config\Manager|mixed
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
     * @param string $ns
     * @return Interfaces\Controller
     * @throws Exception\Factory
     */
    public function buildController($class_name, $ns='Everon\Controller')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name);

            /**
             * @var \Everon\Controller $Controller
             */
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
     * @param string $ns
     * @return Interfaces\View
     * @throws Exception\Factory
     */
    public function buildView($class_name, $template_directory, array $variables, $ns='Everon\View')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name);

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
     * @param string $ns
     * @return Interfaces\TemplateCompiler
     * @throws Exception\Factory
     */
    public function buildTemplateCompiler($class_name, $ns='Everon\View\Template\Compiler')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name);

            /**
             * @var Interfaces\TemplateCompiler $Compiler
             */
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
    public function buildPdoAdapter($dsn, $username, $password, $options)
    {
        try {
            $Adapter = new PdoAdapter($dsn, $username, $password, $options);
            $this->injectDependencies('Everon\PdoAdapter', $Adapter);
            return $Adapter;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('PdoAdapter initialization error', null, $e);
        }
    }


    /**
     * @inheritdoc
     */
    public function buildDomainRepository(SchemaInterface\Table $Table, Interfaces\PdoAdapter $Pdo)
    {
        $name = $Table->getName();
        try {
            $class_name = ucfirst($this->stringUnderscoreToCamel($name));
            $class_name = $this->getFullClassName('Everon\Domain\Repository', $class_name);
            $Repository = new $class_name($Table, $Pdo);
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
    public function buildDomainEntity($class_name, $id, array $data, $ns='Everon\Domain')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name.'\Entity');
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
    public function buildDomainModel($class_name, $ns='Everon\Domain')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name.'\Model');
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
    public function buildDomainManager($class_name, $ns='Everon\Domain\Manager')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name);
            $DomainManager = new $class_name();
            $this->injectDependencies($class_name, $DomainManager);
            return $DomainManager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('DomainManager: "%s" initialization error', $class_name, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildSchemaTable($name, array $columns, array $constraints, array $foreign_keys)
    {
        try {
            $column_list = array();
            foreach ($columns as $column_data) {
                $column_list[] = new Schema\MySql\Column($column_data); //todo: xxx
            }

            $constraint_list = array();
            foreach ($constraints as $constraint_data) {
                $constraint_list[] = new Schema\Constraint($constraint_data);
            }

            $fk_list = array();
            foreach ($foreign_keys as $fk_data) {
                $fk_list[] = new Schema\ForeignKey($fk_data);
            }

            return new Schema\Table($name, $column_list, $constraint_list, $fk_list);
        }
        catch (\Exception $e) {
            throw new Exception\Factory('SchemaTable initialization error', null, $e);
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
     * @param string $ns
     * @return Interfaces\Router|Router
     * @throws Exception\Factory
     */
    public function buildRouter(Interfaces\Config $Config, Interfaces\RequestValidator $Validator, $ns='Everon')
    {
        try {
            $class_name = $this->getFullClassName($ns, 'Router');
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
            $RouteItem = new RequestValidator();
            $this->injectDependencies('Everon\RequestValidator', $RouteItem);
            return $RouteItem;
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
            throw new Exception\Factory('ConfigItemRouter initialization error', null, $e);
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
            throw new Exception\Factory('ConfigItemRouter initialization error', null, $e);
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
            $Logger = new Http\HeaderCollection($headers);
            $this->injectDependencies('Everon\Http\HeaderCollection', $Logger);
            return $Logger;
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
            throw new Exception\Factory('Request initialization error', null, $e);
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