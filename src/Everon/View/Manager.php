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

class Manager implements Interfaces\Manager
{
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Factory;
    use Dependency\Injection\Logger;
    use Dependency\Injection\FileSystem;

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
        $default_extension = $this->getConfigManager()->getConfigValue('application.view.default_extension');

        if ($template_directory !== null) {
            $TemplateDirectory = new \SplFileInfo($template_directory);
            if  ($TemplateDirectory->isDir() === false) {
                $template_directory = null;
            }
        }

        return $this->getFactory()->buildView($view_name, $template_directory, $default_extension, $namespace);
    }

    /**
     * @param $name
     * @param string $namespace
     * @return Interfaces\View
     */
    public function createLayout($name, $namespace='Everon\View')
    {
        $default_extension = $this->getConfigManager()->getConfigValue('application.view.default_extension');
        $default_view = $this->getConfigManager()->getConfigValue('application.view.default_view');
        $namespace .= '\\'.$this->getCurrentThemeName();

        try {
            $template_directory = implode(DIRECTORY_SEPARATOR, [
                $this->getViewDirectory().$this->getCurrentThemeName(), $name, 'templates'
            ]);
            $TemplateDirectory = new \SplFileInfo($template_directory);
            $Layout = $this->getFactory()->buildView($name, $TemplateDirectory->getPathname().DIRECTORY_SEPARATOR, $default_extension, $namespace);
        }
        catch (Exception\Factory $e) {
            $template_directory = implode(DIRECTORY_SEPARATOR, [
                $this->getViewDirectory().$this->getCurrentThemeName(), $default_view, 'templates'
            ]);
            $TemplateDirectory = new \SplFileInfo($template_directory);
            $Layout = $this->getFactory()->buildView($default_view, $TemplateDirectory->getPathname().DIRECTORY_SEPARATOR, $default_extension, $namespace);
        }

        $view_data = $this->getConfigManager()->getConfigValue('view.'.$Layout->getName(), []);
        $IndexTemplate = $Layout->getTemplate('index', $view_data);
        $Layout->setContainer($IndexTemplate);
        
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
        catch (Exception $e) {
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

                    $Compiler->compile($IncludeContext);
                    $data[$name] = $IncludeContext->getCompiled();
                }
            }

            $Context->setData($data);
            $Compiler->compile($Context);
        }
        catch (Exception $e) {
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
}