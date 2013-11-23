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
        $this->Cache = new \Everon\View\Cache();
    }

    /**
     * @param Interfaces\Template $scope_name
     * @param Interfaces\Template $Template
     * @throws \Everon\Exception\ViewManager
     */
    public function compileTemplate($scope_name, Interfaces\Template $Template)
    {
        try {
            $compiled_content = null;
            
            /**
             * @var Interfaces\TemplateCompiler $Compiler
             */
            foreach ($this->compilers as $extension => $compiler_list) {
                foreach ($compiler_list as $Compiler) {
                    if ($this->stringEndsWith($Template->getTemplateFile()->getFilename(), $extension)) {
                        $this->compileOne($scope_name, $Compiler, $Template, $compiled_content);
                    }
                }
            }
            
            $Template->setCompiledContent($compiled_content);
        }
        catch (Exception $e) {
            throw new Exception\ViewManager($e);
        }
    }
    
    public function compileView(Interfaces\View $View)
    {
        $Template = $View->getContainer();
        $this->compileTemplate($View->getName(), $Template);
    }

    /**
     * @param $scope_name
     * @param Interfaces\TemplateCompiler $Compiler
     * @param Interfaces\TemplateContainer $Template
     * @param $compiled_content
     */
    protected function compileOne($scope_name, Interfaces\TemplateCompiler $Compiler, Interfaces\TemplateContainer $Template, &$compiled_content)
    {
        /**
         * @var Interfaces\TemplateContainer $Include
         * @var Interfaces\TemplateContainer $TemplateInclude
         */        
        foreach ($Template->getData() as $name => $Include) {
            if (($Include instanceof Interfaces\TemplateContainer) === false) {
                continue;
            }

            foreach ($Include->getData() as $include_name => $TemplateInclude) {
                if (($TemplateInclude instanceof Interfaces\TemplateContainer) === false) {
                    continue;
                }

                $this->compileOne($scope_name, $Compiler, $TemplateInclude, $compiled_content); //xxx
                $Include->set($include_name, $TemplateInclude->getCompiledContent());
            }

            $Include->setCompiledContent(
                $Compiler->compile($scope_name, $Include->getTemplateContent(), $Include->getData())
            );

            $Template->set($name, $Include->getCompiledContent());
        }

        $compiled_content = $compiled_content ?: $Template->getTemplateContent();
        $compiled_content = $Compiler->compile($scope_name, $compiled_content, $Template->getData());        
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