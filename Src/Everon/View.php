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

use Symfony\Component\Finder\SplFileInfo;

abstract class View implements Interfaces\View
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\Request;

    use Helper\Arrays;
    use Helper\IsIterable;
    use Helper\String\EndsWith;
    use Helper\String\LastTokenToName;
    use Helper\String\StartsWith;

    protected $name = null;
    
    protected $template_directory = null;

    /**
     * @var Interfaces\TemplateContainer
     */
    protected $Container = null;
    
    protected $default_extension = '.htm';

    /**
     * @var array
     */
    protected $vars = [];
    
    

    /**
     * @param $template_directory
     * @param array $vars
     */
    public function __construct($template_directory, array $vars)
    {
        $this->name = $this->stringLastTokenToName(get_class($this));
        $this->template_directory = $template_directory;
        $this->vars = $this->arrayDotKeysToScope($vars);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getTemplateFilename($name)
    {
        if ($this->stringEndsWith($name, $this->default_extension) === false) {
            $name .= $this->default_extension;
        } 
        
        return new \SplFileInfo($this->getTemplateDirectory().$name);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateDirectory()
    {
        return $this->template_directory;
    }

    /**
     * @inheritdoc
     */
    public function setTemplateDirectory($directory)
    {
        $this->template_directory = $directory;
    }

    /**
     * @inheritdoc
     */
    public function getContainer()
    {
        if (is_null($this->Container)) {
            $this->setContainer('');
        }

        return $this->Container;
    }

    /**
     * @inheritdoc
     */
    public function setContainer($Container)
    {
        if ($Container instanceof Interfaces\TemplateContainer) {
            $this->Container = $Container;
        }
        else if (is_string($Container)) {
            $this->Container = $this->getFactory()->buildTemplateContainer($Container, []);
        }
     
        if (is_null($this->Container)) {
            throw new Exception\Template('Invalid Container type');
        }
        
        $data = array_merge($this->Container->getData(), $this->vars);
        $this->Container->setData($data);
    }

    /**
     * @param $action
     * @param Interfaces\Template $DefaultViewTemplate
     * @return Interfaces\Template|Interfaces\TemplateContainer|null
     */
    public function getViewTemplateByAction($action, Interfaces\Template $DefaultViewTemplate)
    {
        $data = $this->getData();
        $ActionTemplate = $this->getTemplate($action, $data);
        $ViewTemplate = $this->getViewTemplate();
        
        if ($ViewTemplate === null) {
            $ViewTemplate = $DefaultViewTemplate; 
        }

        $ViewTemplate->setData(array_merge(
            $data, $ViewTemplate->getData()
        ));

        if ($ActionTemplate !== null) {
            $ViewTemplate->set('View.Body', $ActionTemplate);
        }

        return $ViewTemplate;
    }

    /**
     * @inheritdoc
     */
    public function getViewTemplate()
    {
        return $this->getTemplate('index', $this->vars);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function set($name, $value)
    {
        $this->getContainer()->set($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function get($name, $default=null)
    {
        return $this->getContainer()->get($name);
    }

    /**
     * @inheritdoc
     */
    public function delete($name)
    {
        $this->getContainer()->delete($name);
    }

    /**
     * @inheritdoc
     */
    public function setData(array $data)
    {
        $this->getContainer()->setData($data);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->getContainer()->getData();
    }
    
    protected function getToString()
    {
        return (string) $this->getContainer();
    }

    public function url($url)
    {
        return $this->getRequest()->getLocation().$url;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultExtension($extension)
    {
        $this->default_extension = $extension;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultExtension()
    {
        return $this->default_extension;
    }

    /**
     * @inheritdoc
     */
    public function getFilename()
    {
        return new \SplFileInfo($this->template_directory.$this->name.$this->default_extension);
    }
}