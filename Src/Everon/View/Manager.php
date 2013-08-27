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
    use Helper\String\LastTokenToName;

    protected $views = [];

    protected $compilers = [];

    protected $view_template_directory = null;

    protected $view_cache_directory = null;


    public function __construct(array $compilers, $template_directory, $cache_directory)
    {
        $this->compilers = $compilers;
        $this->view_template_directory = $template_directory;
        $this->view_cache_directory = $cache_directory;
    }

    /**
     * @param Interfaces\TemplateContainer $Template
     * @throws Exception|\Exception
     * @throws Exception\TemplateCompiler
     */
    protected function compileTemplate(Interfaces\TemplateContainer $Template)
    {
        try {
            $compiled_content = null;

            /**
             * @var Interfaces\TemplateCompiler $Compiler
             * @var Interfaces\TemplateContainer $Include
             * @var Interfaces\TemplateContainer $TemplateInclude
             */
            foreach ($this->compilers as $Compiler) {
                foreach ($Template->getData() as $name => $Include) {
                    if (($Include instanceof Interfaces\TemplateContainer) === false) {
                        continue;
                    }

                    foreach ($Include->getData() as $include_name => $TemplateInclude) {
                        if (($TemplateInclude instanceof Interfaces\TemplateContainer) === false) {
                            continue;
                        }

                        $this->compileTemplate($TemplateInclude);
                        $Include->set($include_name, $TemplateInclude->getCompiledContent());
                    }

                    $Include->setCompiledContent(
                        $Compiler->compile($Include->getTemplateContent(), $Include->getData())
                    );
                    $Template->set($name, $Include->getCompiledContent());
                }

                $compiled_content = $compiled_content ?: $Template->getTemplateContent();
                $compiled_content = $Compiler->compile($compiled_content, $Template->getData());
            }
            $Template->setCompiledContent($compiled_content);
        }
        catch (Exception $e) {
            throw $e;
        }
        catch (\Exception $e) {
            throw new Exception\TemplateCompiler($e);
        }
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
        if (isset($this->views[$name]) === false) {
            $template_directory = $this->view_template_directory.$name.DIRECTORY_SEPARATOR;
            if  ((new \SplFileInfo($template_directory))->isDir() === false) {
                throw new Exception\ViewManager('View template directory: "%s" does not exist', $template_directory);
            }

            $this->views[$name] = $this->getFactory()->buildView($name, $template_directory);
        }

        return $this->views[$name];
    }

    /**
     * @param $name
     * @param Interfaces\View $View
     */
    public function setView($name, Interfaces\View $View)
    {
        $this->views[$name] = $View;
    }

    /**
     * @param $name
     * @param $data
     * @return Interfaces\TemplateContainer
     */
    public function getTemplate($name, $data)
    {
        $filename = $this->getTemplateFilename($name);
        return $this->getFactory()->buildTemplate($filename, $data);
    }    

}