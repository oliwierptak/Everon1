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
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Logger;
    use Dependency\Injection\Response;
    use Dependency\Injection\Request;

    use Helper\IsCallable;
    use Helper\ToString;
    use Helper\String\LastTokenToName;

    protected $name = null;
    
    protected $action = null;

    /**
     * @var Interfaces\Module
     */
    protected $Module = null;


    /**
     * @var Config\Interfaces\ItemRouter
     */
    protected $CurrentRoute = null;


    /**
     * @param $action
     * @param $result
     * @return void
     */
    protected abstract function prepareResponse($action, $result);

    protected abstract function response();
    
    
    public function __construct(Interfaces\Module $Module)
    {
        $this->Module = $Module;
    }

    /**
     * @param \Everon\Interfaces\Module $Module
     */
    public function setModule($Module)
    {
        $this->Module = $Module;
    }

    /**
     * @return \Everon\Interfaces\Module
     */
    public function getModule()
    {
        return $this->Module;
    }

    /**
     * @param $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        if ($this->name === null) {
            $this->name = $this->stringLastTokenToName(get_class($this));
        }

        return $this->name;
    }

    /**
     * @param $action
     * @return bool
     * @throws Exception\InvalidControllerMethod
     * @throws Exception\InvalidControllerResponse
     */
    public function execute($action)
    {
        $this->action = $action;
        if ($this->isCallable($this, $action) === false) {
            throw new Exception\InvalidControllerMethod(
                'Controller: "%s@%s" has no action: "%s" defined', [$this->getModule()->getName(), $this->getName(), $action]
            );
        }
        
        $result = $this->{$action}();
        $result = ($result !== false) ? true : $result;
        $this->getResponse()->setResult($result);
        
        if ($result === false) {
            $result_on_error = $this->executeOnError($action);
            if ($result_on_error === null) {
                throw new Exception\InvalidControllerResponse(
                    'Invalid controller response for: "%s@%s"', [$this->getName(),$action]
                );
            }
        }
        
        $this->prepareResponse($action, $result);
        $this->getLogger()->response('[%s] %s : %s', [$this->getResponse()->getResult(), $this->getName(), $action]);
        $this->response();
        return $result;
    }

    /**
     * @param $action
     * @return bool|null
     */
    protected function executeOnError($action)
    {
        $action .= 'OnError';
        if ($this->isCallable($this, $action)) {
            $result = $this->{$action}();
            $result = ($result !== false) ? true : $result;
            return $result;
        }
        
        return null;
    }

    /**
     * @param $name
     * @return null
     * @throws Exception\Controller
     */
    public function getUrl($name)
    {
        $route = $this->getConfigManager()->getConfigValue('router.'.$name);
        if ($route === null) {
            throw new Exception\Controller('Invalid router config name: "%s"', $name);
        }
        
        return $route['url'];
    }

    /**
     * @param Config\Interfaces\ItemRouter $CurrentRoute
     */
    public function setCurrentRoute(Config\Interfaces\ItemRouter $CurrentRoute)
    {
        $this->CurrentRoute = $CurrentRoute;
    }

    /**
     * @return Config\Interfaces\ItemRouter
     */
    public function getCurrentRoute()
    {
        return $this->CurrentRoute;
    }
    
}