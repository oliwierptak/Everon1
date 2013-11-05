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
    
    use Helper\String\EndsWith;
    use Helper\String\LastTokenToName;
    use Helper\ToString;

    protected $name = null;
    protected $template_directory = null;

    /**
     * @var Interfaces\TemplateContainer
     */
    protected $Container = null;
    
    protected $default_extension = null;
    
    protected $variables = [];
    
    protected $ViewTemplate = null;

    
    public function __construct($template_directory, $default_extension, array $variables)
    {
        $this->name = $this->stringLastTokenToName(get_class($this));
        $this->template_directory = $template_directory;
        $this->default_extension = $default_extension;
        $this->variables = $variables;
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
        return $this->name;
    }

    /**
     * @param $name
     * @return \SplFileInfo
     */
    public function getTemplateFilename($name)
    {
        if ($this->stringEndsWith($name, $this->default_extension) === false) {
            $name .= $this->default_extension;
        } 
        
        return new \SplFileInfo($this->getTemplateDirectory().$name);
    }

    public function getTemplateDirectory()
    {
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
    public function getContainer()
    {
        if (is_null($this->Container)) {
            $this->setContainer('');
        }

        return $this->Container;
    }

    /**
     * @param mixed $Container Instance of Interfaces\TemplateContainer, string or array
     * @throws Exception\Template
     */
    public function setContainer($Container)
    {
        if ($Container instanceof Interfaces\TemplateContainer) {
            $this->Container = $Container;
        }
        else if (is_string($Container)) {
            $this->Container = $this->getFactory()->buildTemplateContainer($Container, $this->variables);
        }
     
        if (is_null($this->Container)) {
            throw new Exception\Template('Invalid Container type');
        }
    }

    /**
     * @param $name
     * @param $data
     * @return Interfaces\TemplateContainer
     */
    public function getTemplate($name, $data)
    {
        $Filename = $this->getTemplateFilename($name);

        if ($Filename->isFile() === false) {
            return null;
        }
        
        return $this->getFactory()->buildTemplate($Filename, $data);
    }

    /**
     * @param $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->getContainer()->set($name, $value);
    }

    /**
     * @param $name
     * @param mixed|null $default
     * @return null
     */
    public function get($name, $default=null)
    {
        return $this->getContainer()->get($name);
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->getContainer()->setData($data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->getContainer()->getData();
    }
    
    public function toArray()
    {
        return $this->getContainer()->toArray();
    }

    /**
     * @return array
     */
    protected function getToArray()
    {
        return $this->getContainer()->toArray();
    }

    protected function getToString()
    {
        return (string) $this->getContainer();
    }

    /**
     * @return Interfaces\TemplateContainer
     */
    public function getViewTemplate()
    {
        if ($this->ViewTemplate === null) {
            $this->ViewTemplate = $this->getTemplate('index', $this->variables);
        }
        
        return $this->ViewTemplate;
    }
    
    public function url($url)
    {
        return $url;
    }

}
