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
use Everon\Interfaces;

class Manager implements Interfaces\ViewManager
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Environment;

    use Helper\Arrays;
    use Helper\String\LastTokenToName;
    use Helper\String\EndsWith;

    
    protected $theme_name = 'Main';

    protected $theme_directory = null;
    
    protected $cache_directory = null;
    
    protected $compilers = [];
    
    protected $Cache = null;

    /**
     * @var Helper\Collection
     */
    protected $ThemeCollection = [];


    /**
     * @param array $compilers
     * @param $theme_directory
     * @param $cache_directory
     */
    public function __construct(array $compilers, $theme_directory, $cache_directory)
    {
        $this->compilers = $compilers;
        $this->theme_directory = $theme_directory;
        $this->cache_directory = $cache_directory;
        $this->ThemeCollection = new Helper\Collection([]);
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
    public function compileTemplate($scope_name, Interfaces\Template $Template)
    {
        try {
            $Scope = new Template\Compiler\Scope();
            $Scope->setName($scope_name);
            
            /**
             * @var Interfaces\TemplateCompiler $Compiler
             */
            foreach ($this->compilers as $extension => $compiler_list) {
                foreach ($compiler_list as $Compiler) {
                    //$Compiler->setFileSystem($this->getCache()->getFileSystem());
                    if ($this->stringEndsWith($Template->getTemplateFile()->getFilename(), $extension)) {
                        $this->compileTemplateRecursive($Compiler, $Template, $Scope);
                    }
                }
            }
            
            $Template->setCompiledContent($Scope->getCompiled());
            $Template->setScope($Scope);
        }
        catch (Exception $e) {
            throw new Exception\ViewManager($e);
        }
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
                    $Template->set($name, $IncludeScope->getPhp());
                }
                continue;
            }

            foreach ($Include->getData() as $include_name => $TemplateInclude) {
                if (($TemplateInclude instanceof Interfaces\TemplateContainer) === false) {
                    continue;
                }

                $this->compileTemplateRecursive($Compiler, $TemplateInclude, $Scope);
                $Include->set($include_name, $TemplateInclude->getScope()->getPhp());
            }
            
            $ContentScope = $Compiler->compile($Scope->getName(), $Include->getTemplateContent(), $Include->getData());
            $Include->setCompiledContent($ContentScope->getCompiled());
            $Include->setScope($ContentScope);
            
            $Template->set($name, $Include->getScope()->getPhp());
        }

        $ContentScope = $Compiler->compile($Scope->getName(), $Template->getTemplateContent(), $Template->getData());
        
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
    public function getTheme($name)
    {
        if ($this->ThemeCollection->has($name) === false) {
            $TemplateDirectory = new \SplFileInfo($this->theme_directory.$this->getThemeName().DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'templates');
            if  ($TemplateDirectory->isDir() === false) {
                throw new Exception\ViewManager('Theme: "%s" template directory: "%s" does not exist', [$name, $TemplateDirectory->getPathname()]);
            }

            $default_extension = $this->getConfigManager()->getConfigValue('application.view.default_extension');
            //theme index template
            $TemplateFilename = new \SplFileInfo($TemplateDirectory->getPathname().DIRECTORY_SEPARATOR.'index'.$default_extension);
            if ($TemplateFilename->isFile() === false) { //load default theme first
                throw new Exception\View('Theme index template: "%s" not found for: "%s"', [$TemplateFilename->getPathname(), $name]);
            }

            $view_variables = $this->getConfigManager()->getConfigValue("view.$name");
            $view_variables = $this->arrayDotKeysToScope($view_variables, 'View');
            $IndexTemplate = $this->getFactory()->buildTemplate($TemplateFilename, $view_variables);

            $Theme = $this->createView($name, $TemplateDirectory->getPathname().DIRECTORY_SEPARATOR, 'Everon\View\\'.$this->getThemeName());
            $Theme->setContainer($IndexTemplate);

            $this->ThemeCollection->set($name, $Theme);
        }
        return $this->ThemeCollection->get($name);
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
        if  ((new \SplFileInfo($template_directory))->isDir() === false) {
            throw new Exception\ViewManager('Theme template directory: "%s" does not exist', $template_directory);
        }

        $default_extension = $this->getConfigManager()->getConfigValue('application.view.default_extension');
        $view_variables = $this->getConfigManager()->getConfigValue("view.$name");
        $view_variables = $this->arrayDotKeysToScope($view_variables, 'View');
        return $this->getFactory()->buildView($name, $template_directory, $view_variables, $default_extension, $namespace);
    }

    /**
     * @param string $theme
     */
    public function setThemeName($theme)
    {
        $this->theme_name = $theme;
    }

    /**
     * @return string
     */
    public function getThemeName()
    {
        return $this->theme_name;
    }

    /**
     * @return Interfaces\View
     */
    public function getDefaultTheme()
    {
        return $this->getTheme('Index');
    }
    
}