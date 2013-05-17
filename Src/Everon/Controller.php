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


abstract class Controller implements Interfaces\Controller
{
    use Dependency\ModelManager;
    use Dependency\ViewManager;

    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Request;
    use Dependency\Injection\Router;

    use Helper\ToString;

    /**
     * Controller's name
     * @var string
     */
    protected $name = null;

    /**
     * @param Interfaces\ViewManager $ViewManager
     * @param Interfaces\ModelManager $ModelManager
     */
    public function  __construct(Interfaces\ViewManager $ViewManager, Interfaces\ModelManager $ModelManager)
    {
        $this->ViewManager = $ViewManager;
        $this->ModelManager = $ModelManager;
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

    public function getToString()
    {
        return (string) $this->getView()->getOutput();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getModel($name=null)
    {
        $name = $name === null ? $this->getName() : $name;
        return $this->getModelManager()->getModel($name);
    }
    
    /**
     * @param $name
     * @return Interfaces\View
     */
    public function getView($name=null)
    {
        if ($name === null) {
            $tokens = explode('\\', $this->getName());
            $name = array_pop($tokens);
        }
        
        return $this->getViewManager()->getView($name);
    }

    /**
     * @param $result
     * @param Interfaces\Response $Response
     * @return Interfaces\Response
     */
    public function result($result, Interfaces\Response $Response)
    {
        $Response->setResult($result);
        
        if ($result === false) {
            $data = vsprintf('Invalid response for route: "%s"', [$this->getRouter()->getCurrentRoute()->getName()]);
            $Response->setData($data);
        }
        else {
            $Response->setData($this->getView()->getOutput());
        }

        $Response->send();
        echo $Response->toHtml();
    }

}