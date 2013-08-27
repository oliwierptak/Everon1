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
    protected function injectDependencies($class_name, $Receiver)
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
    protected function getFullClassName($namespace, $class_name)
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
            $Core = new Core\Console();
            $this->injectDependencies('Everon\Core\Console', $Core);
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
            $Core = new Core\Mvc();
            $this->injectDependencies('Everon\Core\Mvc', $Core);
            return $Core;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Core initialization error', null, $e);
        }
    }

    /**
     * @param $name
     * @param $filename
     * @param $data
     * @return Interfaces\Config
     * @throws Exception\Factory
     */
    public function buildConfig($name, $filename, $data)
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
            
            $Config = new $class_name($name, $filename, $data);
            $this->injectDependencies($class_name, $Config);
            return $Config;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Config: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @param Interfaces\ConfigLoader $Loader
     * @param Interfaces\ConfigExpressionMatcher $Matcher
     * @return Config\Manager|mixed
     * @throws Exception\Factory
     */
    public function buildConfigManager(Interfaces\ConfigLoader $Loader, Interfaces\ConfigExpressionMatcher $Matcher)
    {
        try {
            $Manager = new Config\Manager($Loader, $Matcher);
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
     * @param $template_directory
     * @param string $ns
     * @return Interfaces\View
     * @throws Exception\Factory
     */
    public function buildView($class_name, $template_directory, $ns='Everon\View')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name);

            $View = new $class_name($template_directory);
            $this->injectDependencies($class_name, $View);
            return $View;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('View: "%s" initialization error', $class_name, $e);
        }
    }

    /**
     * @param $compilers_to_init
     * @param $view_template_directory
     * @param $view_cache_directory
     * @return Interfaces\ViewManager
     * @throws Exception\Factory
     */
    public function buildViewManager($compilers_to_init, $view_template_directory, $view_cache_directory)
    {
        try {
            $compilers = [];
            foreach ($compilers_to_init as $name => $extension) {
                $compilers[] = $this->buildTemplateCompiler($this->stringUnderscoreToCamel($name));
            }
            
            $Manager = new View\Manager($compilers, $view_template_directory, $view_cache_directory);
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
     * @return mixed
     * @throws Exception\Factory
     */
    public function buildModel($class_name, $ns='Everon\Model')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name);
            $Model = new $class_name();
            $this->injectDependencies($class_name, $Model);
            return $Model;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Model: "%s" initialization error', $class_name, $e);
        }
    }

    /**
     * @param $class_name
     * @param $ns
     * @return Interfaces\ModelManager
     * @throws Exception\Factory
     */
    public function buildModelManager($class_name, $ns='Everon\Model\Manager')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name);
            $ModelManager = new $class_name();
            $this->injectDependencies($class_name, $ModelManager);
            return $ModelManager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ModelManager: "%s" initialization error', $class_name, $e);
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
     * @param Interfaces\RouterValidator $Validator
     * @return Interfaces\Router
     * @throws Exception\Factory
     */
    public function buildRouter(Interfaces\Config $Config, Interfaces\RouterValidator $Validator)
    {
        try {
            $RouteItem = new Router($Config, $Validator);
            $this->injectDependencies('Everon\Router', $RouteItem);
            return $RouteItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Router initialization error', null, $e);
        }
    }

    /**
     * @return Interfaces\RouterValidator
     * @throws Exception\Factory
     */
    public function buildRouterValidator()
    {
        try {
            $RouteItem = new RouterValidator();
            $this->injectDependencies('Everon\RouterValidator', $RouteItem);
            return $RouteItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RouterValidator initialization error', null, $e);
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
            $data['____name'] = $name;
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
            $data['____name'] = $name;
            $RouteItem = new Config\Item\Router($data);
            $this->injectDependencies('Everon\Config\Item\Router', $RouteItem);
            return $RouteItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigItemRouter initialization error', null, $e);
        }
    }

    /**
     * @param array $name
     * @param array $data
     * @return Interfaces\ConfigItem
     * @throws Exception\Factory
     */
    public function buildConfigItemView($name, array $data)
    {
        try {
            $data['____name'] = $name;
            $PageItem = new Config\Item\View($data);
            $this->injectDependencies('Everon\Config\Item\View', $PageItem);
            return $PageItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigItemView initialization error', null, $e);
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
     * @param $directory
     * @return Interfaces\Logger
     * @throws Exception\Factory
     */
    public function buildLogger($directory)
    {
        try {
            $Logger = new Logger($directory);
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
            $Request = new Core\Console\Request($server, $get, $post, $files);
            $this->injectDependencies('Everon\Core\Console\Request', $Request);
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