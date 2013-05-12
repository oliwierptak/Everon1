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

abstract class View implements Interfaces\View, Interfaces\Arrayable
{
    use Dependency\Injection\Factory;

    use Helper\ToArray;


    protected $name = null;
    protected $template_directory = null;
    protected $view_template_directory = null;

    /**
     * @var Interfaces\TemplateContainer
     */
    protected $Output = null;

    /**
     * @var array
     */
    protected $compilers = [];
    

    /**
     * @param array $compilers
     * @param $view_template_directory
     */
    public function __construct(array $compilers, $view_template_directory)
    {
        $this->compilers = $compilers;
        $this->view_template_directory = $view_template_directory;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        if (is_null($this->name)) {
            $this->setName(get_class($this));
        }
        
        return $this->name;
    }

    /**
     * @param $filename
     * @return string
     */
    public function getTemplateFilename($filename)
    {
        return $this->getTemplateDirectory().$filename.'.htm';
    }

    public function getTemplateDirectory()
    {
        if (is_null($this->template_directory)) {
            $tokens = explode('\\', $this->getName());
            $name = array_pop($tokens);
            $this->setTemplateDirectory($this->view_template_directory.$name.DIRECTORY_SEPARATOR);
        }


        return $this->template_directory;
    }

    /**
     * @param $directory
     */
    public function setTemplateDirectory($directory)
    {
        $this->template_directory = $directory;
    }

    /**
     * @return Interfaces\TemplateContainer
     */
    public function getOutput()
    {
        if (is_null($this->Output)) {
            $this->setOutput('');
        }
        
        if (!$this->Output->getCompiledContent()) {
            $this->compileOutput();
        }

        return $this->Output;
    }

    /**
     * @param mixed $Output
     * @throws Exception\Template
     */
    public function setOutput($Output)
    {
        $this->Output = null;

        if ($Output instanceof Interfaces\TemplateContainer) {
            $this->Output = $Output;
        }
        else if (is_string($Output)) {
            $this->Output = $this->getFactory()->buildTemplateContainer($Output, []);
        }
        else if (is_array($Output)) {
            $this->Output = $this->getFactory()->buildTemplateContainer('', $Output);
        }

        if (is_null($this->Output)) {
            throw new Exception\Template('Invalid Output type');
        }

        foreach ($this->getData() as $name => $value) {
            $this->Output->set($name, $value);
        }
    }

    /**
     * @param array $compilers
     */
    public function setCompilers(array $compilers)
    {
        $this->compilers = $compilers;
    }

    public function getCompilers()
    {
        return $this->compilers;
    }

    protected function compileOutput()
    {
        $this->compileTemplate($this->Output);
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
            $includes = $Template->getAllIncludes();
            /**
             * @var Interfaces\TemplateCompiler $Compiler
             * @var Interfaces\TemplateContainer $Include
             * @var Interfaces\TemplateContainer $TemplateInclude
             */
            foreach ($this->compilers as $Compiler) {
                foreach ($includes as $name => $Include) {
                    $template_includes = $Include->getAllIncludes();
                    foreach ($template_includes as $include_name => $TemplateInclude) {
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

    /**
     * @param $name
     * @param $data
     * @return Interfaces\TemplateContainer
     */
    public function getTemplate($name, $data)
    {
        return $this->getFactory()->buildTemplate($this, $name, $data);
    }

    /**
     * @param $name
     * @param mixed $data
     */
    public function set($name, $data)
    {
        $this->data[$name] = $data;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $action
     * @param array $data
     */
    public function setTemplateFromAction($action, array $data)
    {
        $filename = $this->getTemplateFilename($action);
        if ($this->Output === null && is_file($filename)) { //only overwrite Output if not set before and template file exists
            $this->Output = $this->getTemplate($action, $data);
        }
    }
    
}
