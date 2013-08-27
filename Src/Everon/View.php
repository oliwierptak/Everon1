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
    
    use Helper\String\LastTokenToName;
    use Helper\ToString;
    use Helper\ToArray;

    protected $data = [];
    protected $name = null;
    protected $template_directory = null;

    /**
     * @var Interfaces\TemplateContainer
     */
    protected $Container = null;


    /**
     * @param $template_directory
     */
    public function __construct($template_directory)
    {
        $this->name = $this->stringLastTokenToName(get_class($this));
        $this->template_directory = $template_directory;
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
     * @param $filename
     * @return \SplFileInfo
     */
    public function getTemplateFilename($filename)
    {
        return new \SplFileInfo($this->getTemplateDirectory().$filename.'.htm');
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
    public function getOutput()
    {
        if (is_null($this->Container)) {
            $this->setOutput('');
        }

        return $this->Container;
    }

    /**
     * @param mixed $Output
     * @throws Exception\Template
     */
    public function setOutput($Output)
    {
        if ($Output instanceof Interfaces\TemplateContainer) {
            $this->Container = $Output;
        }
        else if (is_string($Output)) {
            $this->Container = $this->getFactory()->buildTemplateContainer($Output, []);
        }
        else if (is_array($Output)) {
            $this->Container = $this->getFactory()->buildTemplateContainer('', $Output);
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
        $filename = $this->getTemplateFilename($name);
        return $this->getFactory()->buildTemplate($filename, $data);
    }

    /**
     * @param $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->getOutput()->set($name, $value);
    }

    /**
     * @param $name
     * @param mixed|null $default
     * @return null
     */
    public function get($name, $default=null)
    {
        return $this->getOutput()->get($name);
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->getOutput()->setData($data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->getOutput()->getData();
    }

    /**
     * @param $action
     * @param array $data
     */
    public function setOutputFromAction($action, array $data)
    {
        $Filename = $this->getTemplateFilename($action);
        if ($Filename->isFile()) {
            $this->Container = $this->getTemplate($action, $data);
        }
    }

    /**
     * @return array
     */
    public function getToArray()
    {
        return $this->getOutput()->toArray();
    }

    public function getToString()
    {
        return (string) $this->getOutput();
    }

}
