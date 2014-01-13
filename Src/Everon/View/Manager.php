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

    use Helper\String\LastTokenToName;
    use Helper\String\EndsWith;


    /**
     * @var Helper\Collection
     */
    protected $ViewCollection = [];

    protected $view_directory = null;
    
    protected $compilers = [];
    
    protected $Cache = null;


    /**
     * @param array $compilers
     * @param $view_directory
     */
    public function __construct(array $compilers, $view_directory)
    {
        $this->compilers = $compilers;
        $this->ViewCollection = new Helper\Collection([]);
        $this->view_directory = $view_directory;
    }
    
    public function getCache()
    {
        $cache_directory = getcwd().'../../Tmp/cache/view/';
        if (!is_dir($cache_directory)) {
            throw new Exception\ViewManager('cache dir does not existt');
        }
        
        if ($this->Cache === null) {
            $FileSystem = $this->getFactory()->buildFileSystem($cache_directory);
            $this->Cache = $this->getFactory()->buildViewCache($FileSystem);
        }
        
        return $this->Cache;
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
                    $Compiler->setFileSystem($this->getCache()->getFileSystem());
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
     * @param Interfaces\TemplateCompiler $Compiler
     * @param Interfaces\TemplateContainer $Template
     * @param $Scope
     */
    protected function compileTemplateRecursive(Interfaces\TemplateCompiler $Compiler, 
        Interfaces\TemplateContainer $Template, Interfaces\TemplateCompilerScope $Scope)
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
     * @return mixed
     * @throws Exception\ViewManager
     */
    public function getView($name)
    {
        if ($this->ViewCollection->has($name) === false) {
            $template_directory = $this->view_directory.$name.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
            if  ((new \SplFileInfo($template_directory))->isDir() === false) {
                throw new Exception\ViewManager('View template directory: "%s" does not exist', $template_directory);
            }

            $default_extension = $this->getConfigManager()->getConfigValue('application.view.default_extension');
            $ViewVariables = $this->getConfigManager()->getConfigValue("view.$name");
            $View = $this->getFactory()->buildView($name, $template_directory, $ViewVariables);
            $View->setDefaultExtension($default_extension);
            $this->ViewCollection->set($name, $View);
        }

        return $this->ViewCollection->get($name);
    }

    /**
     * @param $name
     * @param Interfaces\View $View
     */
    public function setView($name, Interfaces\View $View)
    {
        $this->ViewCollection->set($name, $View);
    }

    /**
     * @return Interfaces\View
     */
    public function getDefaultView()
    {
        return $this->getView('Index');
    }
    
}