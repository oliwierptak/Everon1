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
     * @return void
     * @throws Exception\InvalidControllerMethod
     * @throws Exception\InvalidControllerResponse
     */
    public function execute($action)
    {
        $this->action = $action;
        if ($this->isCallable($this, $action) === false) {
            throw new Exception\InvalidControllerMethod(
                'Controller: "%s" has no action: "%s" defined', [$this->getName(), $action]
            );
        }
        
        $result = $this->{$action}();
        $result = ($result !== false) ? true : $result;
        $this->getResponse()->setResult($result);
        
        if ($result === false) {
            throw new Exception\InvalidControllerResponse(
                'Invalid controller response for action: "%s:%s"', [$this->getName(),$action]
            );
        }
        
        $this->prepareResponse($action, $result);
        $this->getLogger()->response('[%s] %s : %s', [$this->getResponse()->getResult(), $this->getName(), $action]);
        $this->response();
    }

}