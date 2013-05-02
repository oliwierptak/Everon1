<?php
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
     * @param $namespace
     * @param $class_name
     * @return string
     */
    protected function getFullClassName($namespace, $class_name)
    {
        $class = $namespace.'\\'.$class_name;
        return $class;
    }

    /**
     * @return Interfaces\Core
     * @throws Exception\Factory
     */
    public function buildCore()
    {
        try {
            $Core = new Core();
            $this->getDependencyContainer()->inject('Everon\Core', $this, $Core);
            return $Core;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Core initialization error', $e);
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
            $class_name = ucfirst($this->underscoreToCamel($name));
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
            $this->getDependencyContainer()->inject($class_name, $this, $Config);
            return $Config;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Config: "%s" initialization error', $name, $e);
        }
    }

    /**
     * @param Interfaces\ConfigExpressionMatcher $Matcher
     * @param $directory
     * @param $cache_directory
     * @return Config\Manager|mixed
     * @throws Exception\Factory
     */
    public function buildConfigManager(Interfaces\ConfigExpressionMatcher $Matcher, $directory, $cache_directory)
    {
        try {
            $Manager = new Config\Manager($Matcher, $directory, $cache_directory);
            $this->getDependencyContainer()->inject('Everon\Config\Manager', $this, $Manager);
            return $Manager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigManager initialization error', $e);
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
            $this->getDependencyContainer()->inject('Everon\Config\ExpressionMatcher', $this, $ExpressionMatcher);
            return $ExpressionMatcher;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ConfigExpressionMatcher initialization error', $e);
        }
    }

    /**
     * @param $class_name
     * @param Interfaces\View $View
     * @param string $ns
     * @return Controller|Interfaces\Controller
     * @throws Exception\Factory
     */
    public function buildController($class_name, Interfaces\View $View, $ns='Everon\Controller')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name);

            /**
             * @var \Everon\Controller $Controller
             */
            $Controller = new $class_name($View);
            $Controller = $this->getDependencyContainer()->inject($class_name, $this, $Controller);
            return $Controller;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Controller: "%s" initialization error', $class_name, $e);
        }
    }

    /**
     * @param $class_name
     * @param array $compilers_to_init
     * @param string $ns
     * @return Interfaces\View
     * @throws Exception\Factory
     */
    public function buildView($class_name, array $compilers_to_init, $ns='Everon\View')
    {
        try {
            $class_name = $this->getFullClassName($ns, $class_name);

            $compilers = [];
            for ($a=0; $a<count($compilers_to_init); ++$a) {
                $compilers[] = $this->buildTemplateCompiler($compilers_to_init[$a]);
            }

            $View = new $class_name($compilers);
            $View = $this->getDependencyContainer()->inject($class_name, $this, $View);
            return $View;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('View: "%s" initialization error', $class_name, $e);
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
            $Model = $this->getDependencyContainer()->inject($class_name, $this, $Model);
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
            $ModelManager = $this->getDependencyContainer()->inject($class_name, $this, $ModelManager);
            return $ModelManager;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('ModelManager: "%s" initialization error', $class_name, $e);
        }
    }

    /**
     * @param null $data
     * @param Interfaces\Collection $Headers
     * @return Interfaces\Response
     * @throws Exception\Factory
     */
    public function buildResponse($data=null, Interfaces\Collection $Headers=null)
    {
        try {
            $RouteItem = new Response($data, $Headers);
            $RouteItem = $this->getDependencyContainer()->inject('Everon\Response', $this, $RouteItem);
            return $RouteItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Response initialization error', $e);
        }
    }

    /**
     * @param Interfaces\Request $Request
     * @param Interfaces\RouterConfig $Config
     * @return Interfaces\Router
     * @throws Exception\Factory
     */
    public function buildRouter(Interfaces\Request $Request, Interfaces\RouterConfig $Config)
    {
        try {
            $RouteItem = new Router($Request, $Config);
            $RouteItem = $this->getDependencyContainer()->inject('Everon\Router', $this, $RouteItem);
            return $RouteItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Router initialization error', $e);
        }
    }

    /**
     * @param array $data
     * @return Interfaces\RouteItem
     * @throws Exception\Factory
     */
    public function buildRouteItem(array $data)
    {
        try {
            $RouteItem = new RouteItem($data);
            $RouteItem = $this->getDependencyContainer()->inject('Everon\RouteItem', $this, $RouteItem);
            return $RouteItem;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('RouteItem initialization error', $e);
        }
    }

    /**
     * @param Interfaces\View $View
     * @param $filename
     * @param array $template_data
     * @return Interfaces\TemplateContainer
     * @throws Exception\Factory
     */
    public function buildTemplate(Interfaces\View $View, $filename, array $template_data)
    {
        try {
            $Template = new View\Template($View->getTemplateFilename($filename), $template_data);
            $Template = $this->getDependencyContainer()->inject('Everon\View\Template', $this, $Template);
            return $Template;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Template initialization error', $e);
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
            $TemplateContainer = $this->getDependencyContainer()->inject('Everon\View\Template\Container', $this, $TemplateContainer);
            return $TemplateContainer;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('TemplateContainer initialization error', $e);
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
            $Compiler = new $class_name($this);
            $Compiler = $this->getDependencyContainer()->inject($class_name, $this, $Compiler);
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
            $Logger = $this->getDependencyContainer()->inject('Everon\Logger', $this, $Logger);
            return $Logger;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Logger initialization error', $e);
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
            $Logger = $this->getDependencyContainer()->inject('Everon\Http\HeaderCollection', $this, $Logger);
            return $Logger;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('HttpHeaderCollection initialization error', $e);
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
            $Request = $this->getDependencyContainer()->inject('Everon\Request', $this, $Request);
            return $Request;
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Request initialization error', $e);
        }
    }

/* 
    public function buildViewComponent($data)
    {
        $Component = new View\Template\Component\Everon($data);

        return $Component;
    }*/

}