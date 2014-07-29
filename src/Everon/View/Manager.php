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

use Everon\Dependency as EveronDependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces\Collection;

class Manager implements Interfaces\Manager
{
    use Dependency\ViewWidgetManager;
    use EveronDependency\Injection\ConfigManager;
    use EveronDependency\Injection\Factory;
    use EveronDependency\Injection\Logger;
    use EveronDependency\Injection\FileSystem;
    use EveronDependency\Injection\Router;

    use Helper\Arrays;
    use Helper\IsIterable;
    use Helper\String\LastTokenToName;
    use Helper\String\EndsWith;


    protected $current_theme_name = 'Main';

    protected $view_directory = null;

    protected $cache_directory = null;

    protected $compilers = [];

    /**
     * @var Collection
     */
    protected $LayoutCollection = null;

    /**
     * @var Interfaces\WidgetManager
     */
    protected $WidgetManager = null;



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
    }

    protected function getDefaultCompiler()
    {
        $default_extension = $this->getConfigManager()->getConfigValue('application.view.default_extension');
        if (isset($this->compilers[$default_extension]) === false) {
            throw new Exception\ViewManager('Default template compiler not set for: "%s"', $default_extension);
        }

        return $this->compilers[$default_extension];
    }

    /**
     * @param $view_name
     * @param $template_directory
     * @param string $namespace
     * @internal param $layout_name
     * @internal param $layout_name
     * @return Interfaces\View
     */
    public function createView($view_name, $template_directory = null, $namespace = 'Everon\View')
    {
        /*
        $view_data = $this->getConfigManager()->getConfigValue('view.'.$request_name, null);
        if ($view_data === null) {
            throw new Exception\ConfigItem('Undefined view layout data for: "%s"', $name);
        }*/
        
        $default_extension = $this->getConfigManager()->getConfigValue('application.view.default_extension');

        if ($template_directory !== null) {
            $TemplateDirectory = new \SplFileInfo($template_directory);
            if  ($TemplateDirectory->isDir() === false) {
                $template_directory = null;
            }
        }
        
        $View = $this->getFactory()->buildView($view_name, $template_directory, $default_extension, $namespace);
        $View->setViewManager($this);
        return $View;
    }

    /**
     * @param $name
     * @param string $namespace
     * @return Interfaces\View
     * @throws \Everon\Exception\ViewManager
     */
    public function createLayout($name, $namespace='Everon\View')
    {
        $default_view = $this->getConfigManager()->getConfigValue('application.view.default_view');
        $namespace .= '\\'.$this->getCurrentThemeName();
        
        $makeLayout = function($name) use ($namespace) {
            $template_directory = implode(DIRECTORY_SEPARATOR, [
                $this->getViewDirectory().$this->getCurrentThemeName(), $name, 'templates'
            ]);
            $TemplateDirectory = new \SplFileInfo($template_directory);
            $Layout = $this->createView($name, $TemplateDirectory->getPathname().DIRECTORY_SEPARATOR, $namespace);

            $view_data = $this->getConfigManager()->getConfigValue('view.'.$Layout->getName(), null);
            if ($view_data === null) {
                throw new Exception\ConfigItem('Undefined view layout data for: "%s"', $name);
            }
            
            $IndexTemplate = $Layout->getTemplate('index', $view_data);
            if ($IndexTemplate === null) {
                throw new Exception\ViewManager('Invalid index template for layout: "%s"', $name);
            }

            $Layout->setContainer($IndexTemplate);
            return $Layout;
        };

        try {
            $Layout = $makeLayout($name);
        }
        catch (Exception $e) {
            $Layout = $makeLayout($default_view);
        }
        
        return $Layout;
    }

    /**
     * @inheritdoc
     */
    public function compileView($action, Interfaces\View $View)
    {
        $Context = $this->getFactory()->buildTemplateCompilerContext();
        $Context->setScopeName($View->getName());
        $Context->setPhp($View->getContainer()->getTemplateContent());
        $Context->setData($View->getContainer()->getData());
        $Context->setScope($View);
        
        try {
            /**
             * @var Interfaces\TemplateCompiler $Compiler
             */
            $Compiler = $this->getDefaultCompiler()[0];
            $this->compile($Compiler, $Context);
            
            $View->getContainer()->setCompiledContent($Context->getCompiled());
        }
        catch (\Exception $e) {
            throw new Exception\ViewManager($e);
        }
    }

    /**
     * @inheritdoc
     */
    protected function compile(Interfaces\TemplateCompiler $Compiler, Interfaces\TemplateCompilerContext $Context)
    {
        try {
            /**
             * @var Interfaces\View $Include
             * @var Interfaces\TemplateCompilerContext $Context
             */
            $data = $Context->getData();
            foreach ($data as $name => $Include) {
                if (($Include instanceof Interfaces\View)) {
                    $IncludeContext = $this->getFactory()->buildTemplateCompilerContext();
                    $IncludeContext->setScopeName($Include->getName());
                    $IncludeContext->setPhp($Include->getContainer()->getTemplateContent());
                    $IncludeContext->setData($Include->getData());
                    $IncludeContext->setScope($Include);
                    
                    $this->compile($Compiler, $IncludeContext);
                    $data[$name] = $IncludeContext->getCompiled();
                }

                /**
                 * @var Interfaces\TemplateContainer $Include
                 */
                if (($Include instanceof Interfaces\TemplateContainer)) {
                    $IncludeContext = $this->getFactory()->buildTemplateCompilerContext();
                    $IncludeContext->setScopeName($name);
                    $IncludeContext->setPhp($Include->getTemplateContent());
                    $IncludeContext->setData($Include->getData());
                    $IncludeContext->setScope($Context->getScope());

                    $Compiler->compile($IncludeContext);
                    $data[$name] = $IncludeContext->getCompiled();
                }
            }

            $Context->setData($data);
            $Compiler->compile($Context);
        }
        catch (\Exception $e) {
            throw new Exception\ViewManager($e);
        }
    }

    public function getCompilers()
    {
        return $this->compilers;
    }

    /**
     * @inheritdoc
     */
    public function getLayoutByName($name)
    {
        if ($this->LayoutCollection->has($name) === false) {
            $View = $this->createLayout($name);
            $this->LayoutCollection->set($name, $View);
        }

        return $this->LayoutCollection->get($name);
    }

    /**
     * @param Interfaces\View $View
     */
    public function setLayoutByLayoutName(Interfaces\View $View)
    {
        $this->LayoutCollection->set($View->getName(), $View);
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
     * @param \Everon\View\Interfaces\WidgetManager $WidgetManager
     */
    public function setWidgetManager(Interfaces\WidgetManager $WidgetManager)
    {
        $this->WidgetManager = $WidgetManager;
    }

    /**
     * @return \Everon\View\Interfaces\WidgetManager
     */
    public function getWidgetManager()
    {
        if ($this->WidgetManager === null) {
            $this->WidgetManager = $this->getFactory()->buildViewWidgetManager($this);
        }
        return $this->WidgetManager;
    }

    public function renderWidget($name)
    {
        return $this->getWidgetManager()->includeWidget($name);
    }

}