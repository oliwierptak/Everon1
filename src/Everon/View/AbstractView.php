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

use Everon\Helper;
use Everon\Exception;
use Everon\Dependency;

abstract class AbstractView implements Interfaces\View
{
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Factory;
    use Dependency\Injection\Request;

    use Helper\Arrays;
    use Helper\IsCallable;
    use Helper\IsIterable;
    use Helper\String\EndsWith;
    use Helper\String\LastTokenToName;
    use Helper\String\StartsWith;

    protected $name = null;

    protected $template_directory = null;

    /**
     * @var Interfaces\Template
     */
    protected $Container = null;

    protected $default_extension = '.php';
    
    protected $index_executed = false;


    /**
     * @param $template_directory
     * @param $default_extension
     */
    public function __construct($template_directory, $default_extension=null)
    {
        $this->name = $this->stringLastTokenToName(get_called_class());
        $this->template_directory = $template_directory;
        if ($default_extension !== null) {
            $this->default_extension = $default_extension;
        }
    }

    protected function getToString()
    {
        return (string) $this->getContainer();
    }

    /**
     * @param $name
     * @return \SplFileInfo
     */
    protected function getTemplateFilename($name)
    {
        if ($this->stringEndsWith($name, $this->default_extension) === false) {
            $name .= $this->default_extension;
        }

        return new \SplFileInfo($this->getTemplateDirectory().$name);
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
        if ($this->Container === null) {
            $this->setContainer('');
        }

        if ($this->Container === null) {
            throw new Exception\View('View container not set');
        }

        return $this->Container;
    }

    /**
     * @inheritdoc
     */
    public function setContainer($Container)
    {
        if ($Container instanceof Interfaces\TemplateContainer) {
            if ($this->Container !== null) {
                $data = $this->arrayMergeDefault($this->Container->getData(), $Container->getData());
                $Container->setData($data);
            }
            $this->Container = $Container;
        }
        else if (is_string($Container)) {
            $data = [];
            if ($this->Container !== null) {
                $data = $this->Container->getData();
            }
            $this->Container = $this->getFactory()->buildTemplateContainer($Container, $data);
        } 

        if ($this->Container === null) {
            throw new Exception\Template('Invalid container type');
        }

        $this->set('View', $this); //todo: meh
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
        $data = $this->arrayMergeDefault($data, $this->getContainer()->getData());
        $this->getContainer()->setData($data);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->getContainer()->getData();
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
        return new \SplFileInfo($this->getTemplateDirectory().$this->getName().$this->getDefaultExtension());
    }

    /**
     * @inheritdoc
     */
    public function execute($action)
    {
        if ($this->index_executed === false) {
            $default_action = 'index';
            if (strcasecmp($action, $default_action) !== 0) {
                if ($this->isCallable($this, $default_action)) {
                    $this->{$default_action}();
                }
            }
        }
        $this->index_executed = true;

        if ($this->isCallable($this, $action)) {
            return $this->{$action}();
        }
        
        return null;
    }


    /**
     * @inheritdoc
     */
    public function getUrl($name, $query=[], $get=[])
    {
        $Item = $this->getConfigManager()->getConfigByName('router')->getItemByName($name);
        if ($Item === null) {
            throw new Exception\Controller('Invalid router config name: "%s"', $name);
        }

        $Item->compileUrl($query);
        $url = $Item->getParsedUrl();

        $get_url = '';
        if (empty($get) === false) {
            $get_url = http_build_query($get);
            if (trim($get_url) !== '') {
                $get_url = '?'.$get_url;
            }
        }

        return $url.$get_url;
    }

}