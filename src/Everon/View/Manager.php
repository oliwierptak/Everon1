<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View;

use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces\Collection;
use Everon\View\Interfaces;

class Manager implements Interfaces\Manager
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\ConfigManager;

    use Helper\Arrays;
    use Helper\IsIterable;
    use Helper\String\LastTokenToName;
    use Helper\String\EndsWith;

    
    protected $current_theme_name = 'Main';

    protected $view_directory = null;
    
    protected $cache_directory = null;
    
    protected $compilers = [];
    
    protected $Cache = null;

    /**
     * @var Collection
     */
    protected $ThemeCollection = [];

    /**
     * @var Collection
     */
    protected $WidgetCollection = [];


    /**
     * @param array $compilers
     * @param $view_directory
     * @param $cache_directory
     */
    public function __construct(array $compilers, $view_directory, $cache_directory)
    {
        $this->compilers = $compilers;
        $this->view_directory = $view_directory;
        $this->cache_directory = $cache_directory;
        $this->ThemeCollection = new Helper\Collection([]);
        $this->WidgetCollection = new Helper\Collection([]);
    }
    
    public function getCache()
    {
        if (is_dir($this->cache_directory) === false) {
            throw new Exception\ViewManager('Cache directory does not exist');
        }
        
        if ($this->Cache === null) {
            $FileSystem = $this->getFactory()->buildFileSystem($this->cache_directory);
            $this->Cache = $this->getFactory()->buildViewCache($FileSystem);
        }
        
        return $this->Cache;
    }

    /**
     * @inheritdoc
     */
    public function compileView($action, Interfaces\View $View)
    {
        /**
         * @var $Template Interfaces\Template
         */
        $Template = $View->getContainer();

        if ($this->getConfigManager()->getConfigValue('application.cache.view')) {
            $this->getCache()->handle($this, $View, $action);
        }
        else {
            $this->compileTemplate($View->getName(), $Template);
        }
    }

    /**
     * @inheritdoc
     */
    public function compileTemplate($scope_name, Interfaces\TemplateContainer $Template)
    {
        try {
            $Scope = new Template\Compiler\Scope();
            $Scope->setName($scope_name);
            
            if ($Template instanceof Interfaces\Template) {
                /**
                 * @var Interfaces\TemplateCompiler $Compiler
                 */
                foreach ($this->compilers as $extension => $compiler_list) {
                    foreach ($compiler_list as $Compiler) {
                        if ($this->stringEndsWith($Template->getTemplateFile()->getFilename(), $extension)) {
                            $this->compileTemplateRecursive($Compiler, $Template, $Scope);
                        }
                    }
                }
            }
            else {
                $compiler_list = $this->getDefaultCompilers();
                foreach ($compiler_list as $Compiler) {
                    $this->compileTemplateRecursive($Compiler, $Template, $Scope);
                }
            }

            $Template->setCompiledContent($Scope->getCompiled());
            $Template->setScope($Scope);
        }
        catch (Exception $e) {
            throw new Exception\ViewManager($e);
        }
    }
    
    protected function getDefaultCompilers()
    {
        $default_extension = $this->getConfigManager()->getConfigValue('application.view.default_extension');
        if (isset($this->compilers[$default_extension]) === false) {
            throw new Exception\ViewManager('Default template compiler not set for: "%s"', $default_extension);
        }
        
        return $this->compilers[$default_extension];
    }

    /**
     * @param Interfaces\TemplateCompiler $Compiler
     * @param Interfaces\TemplateContainer $Template
     * @param $Scope
     */
    protected function compileTemplateRecursive(Interfaces\TemplateCompiler $Compiler, Interfaces\TemplateContainer $Template, Interfaces\TemplateCompilerScope $Scope)
    {
        /**
         * @var Interfaces\TemplateContainer $Include
         * @var Interfaces\TemplateContainer $TemplateInclude
         */        
        foreach ($Template->getData() as $name => $Include) {
            if (($Include instanceof Interfaces\TemplateContainer) === false) {
                if (is_string($Include)) {
                    $IncludeScope = $Compiler->compile($Scope->getName(), $Include, $Template->getData());
                    $Template->set($name, $IncludeScope->getCompiled());
                }
                continue;
            }

            foreach ($Include->getData() as $include_name => $TemplateInclude) {
                if (($TemplateInclude instanceof Interfaces\TemplateContainer) === false) {
                    continue;
                }

                $this->compileTemplateRecursive($Compiler, $TemplateInclude, $Scope);
                $Include->set($include_name, $TemplateInclude->getScope()->getCompiled());
            }
            
            $ContentScope = $Compiler->compile($Scope->getName(), $Include->getTemplateContent(), $Include->getData());
            $Include->setCompiledContent($ContentScope->getCompiled());
            $Include->setScope($ContentScope);
            
            $Template->set($name, $Include->getScope()->getCompiled());
        }

        $ContentScope = $Compiler->compile($Scope->getName(), $Template->getTemplateContent(), $Template->getData());

        $Scope->setCompiled($ContentScope->getCompiled());
        $Scope->setPhp($ContentScope->getPhp());
        $Scope->setData($ContentScope->getData());

        //compile whole
        $ContentScope = $Compiler->compile($Scope->getName(), $Scope->getCompiled(), $Scope->getData());

        $Scope->setCompiled($ContentScope->getCompiled());
        $Scope->setPhp($ContentScope->getPhp());
        $Scope->setData($ContentScope->getData());
    }

    public function getCompilers()
    {
        return $this->compilers;
    }

    /**
     * @param $name
     * @param Interfaces\View $View
     */
    public function setTheme($name, Interfaces\View $View)
    {
        $this->ThemeCollection->set($name, $View);
    }

    /**
     * @inheritdoc
     */
    public function getTheme($theme_name, $view_name)
    {
        if ($this->ThemeCollection->has($theme_name) === false) {
            $TemplateDirectory = new \SplFileInfo($this->getViewDirectory().$theme_name.DIRECTORY_SEPARATOR.$view_name.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR);
            if ($TemplateDirectory->isDir() === false) { 
                $view_name = 'Index';//revert to default
                $TemplateDirectory = new \SplFileInfo($this->getViewDirectory().$theme_name.DIRECTORY_SEPARATOR.$view_name.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR);
                if ($TemplateDirectory->isDir() === false) {
                    throw new Exception\ViewManager('View: "%s" template directory: "%s" does not exist', [$view_name, $TemplateDirectory->getPathname()]);
                }
            }
            
            $Theme = $this->createView($view_name, $TemplateDirectory->getPathname(), 'Everon\View\\'.$theme_name);
            $view_variables = $this->getConfigManager()->getConfigValue("view.$view_name", []);
            $IndexTemplate = $Theme->getTemplate('index', $view_variables);
            
            if ($IndexTemplate === null) { //fallback to default theme and view
                $TemplateDirectory = new \SplFileInfo($this->getViewDirectory().$this->getCurrentThemeName().DIRECTORY_SEPARATOR.'index'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR);
                $Theme = $this->createView('index', $TemplateDirectory->getPathname(), 'Everon\View\\'.$this->getCurrentThemeName());
                $view_variables = $this->getConfigManager()->getConfigValue("view.$view_name", []);
                $IndexTemplate = $Theme->getTemplate('index', $view_variables);
            }
            
            $Theme->setContainer($IndexTemplate);
            
            $this->ThemeCollection->set($theme_name, $Theme);
        }
        return $this->ThemeCollection->get($theme_name);
    }

    /**
     * @param $name
     * @param $template_directory
     * @param $namespace
     * @return Interfaces\View
     * @throws \Everon\Exception\ViewManager
     */
    public function createView($name, $template_directory, $namespace='Everon\View')
    {
        $default_extension = $this->getConfigManager()->getConfigValue('application.view.default_extension');
        
        $TemplateDirectory = new \SplFileInfo($template_directory);
        if  ($TemplateDirectory->isDir() === false) {  //fallback to theme dir
            $theme_dir = $this->view_directory.$this->getCurrentThemeName().DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'templates';
            $TemplateDirectory = new \SplFileInfo($theme_dir);
            if  ($TemplateDirectory->isDir() === false) {
                throw new Exception\ViewManager('View template directory: "%s" does not exist', $template_directory);
            }
        }
        
        try {
            //try to load module view
            return $this->getFactory()->buildView($name, $TemplateDirectory->getPathname().DIRECTORY_SEPARATOR, $default_extension, $namespace);
        }
        catch (Exception\Factory $e) { //fallback to theme view
            $namespace = 'Everon\View\\'.$this->getCurrentThemeName();
            return $this->getFactory()->buildView($name, $TemplateDirectory->getPathname().DIRECTORY_SEPARATOR, $default_extension, $namespace);
        }
    }

    public function createViewWidget($name, $namespace='Everon\View\Widget')
    {
        $template_directory = $this->getViewDirectory().$this->getCurrentThemeName().DIRECTORY_SEPARATOR.'Widget'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
        return $this->createView('Base', $template_directory, $namespace);
    }

    /**
     * @inheritdoc
     */
    public function createWidget($name, $namespace='Everon\View')
    {
        $ViewWidget = $this->createViewWidget($name);
        $Widget = $this->getFactory()->buildViewWidget($name, $namespace.'\\'.$this->getCurrentThemeName().'\Widget');
        $Widget->setView($ViewWidget);
        return $Widget;
    }

    /**
     * @param string $theme
     */
    public function setCurrentThemeName($theme)
    {
        $this->current_theme_name = $theme;
    }

    /**
     * @return string
     */
    public function getCurrentThemeName()
    {
        return $this->current_theme_name;
    }

    /**
     * @return Interfaces\View
     */
    public function getCurrentTheme($view_name)
    {
        return $this->getTheme($this->getCurrentThemeName(), $view_name);
    }

    /**
     * @param string $cache_directory
     */
    public function setCacheDirectory($cache_directory)
    {
        $this->cache_directory = $cache_directory;
    }

    /**
     * @return string
     */
    public function getCacheDirectory()
    {
        return $this->cache_directory;
    }

    /**
     * @inheritdoc
     */
    public function setViewDirectory($theme_directory)
    {
        $this->view_directory = $theme_directory;
    }

    /**
     * @return string
     */
    public function getViewDirectory()
    {
        return $this->view_directory;
    }

    /**
     * @inheritdoc
     */
    public function includeWidget($name)
    {
        if ($this->WidgetCollection->has($name) === false) {
            $Widget = $this->createWidget($name);
            $this->WidgetCollection->set($name,$Widget);
        }

        return $this->WidgetCollection->get($name)->render();
    }
}