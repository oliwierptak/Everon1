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

    /**
     * Controller's name
     * @var string
     */
    protected $name = null;

    /**
     * @return void
     */
    protected abstract function prepareResponse();


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
     * @return mixed|void
     * @throws Exception\InvalidControllerMethod
     * @throws Exception\InvalidControllerResponse
     */
    public function execute($action)
    {
        if ($this->isCallable($this, $action) === false) {
            throw new Exception\InvalidControllerMethod(
                'Controller: "%s" has no action: "%s" defined', [$this->getName(), $action]
            );
        }
        
        $this->getLogger()->actions('%s : %s', [$this->getName(), $action]);

        $result = $this->{$action}();
        $Response = $this->getResponse();
        $Response->setResult($result);

        if ($result === false) {
            throw new Exception\InvalidControllerResponse(
                'Invalid controller response for action: "%s:%s"', [$this->getName(),$action]
            );
        }
        else {
            $this->prepareResponse();
        }
    }

}