<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest;

use Everon\Interfaces;
use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Http;
use Everon\Rest;

/**
 * @method Http\Interfaces\Response getResponse()
 */
abstract class Controller extends \Everon\Controller implements Rest\Interfaces\Controller
{
    use Dependency\Injection\DomainManager;
    use Dependency\Injection\Environment;
    use Dependency\Injection\Factory;
    use Dependency\Injection\ModuleManager;

    
    use Helper\Arrays;
    use Helper\IsIterable;
    use Helper\String\StartsWith;

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
                'Controller: "%s@%s" has no action: "%s" defined', [$this->getModule()->getName(), $this->getName(), $action]
            );
        }

        $result = $this->{$action}();
        $result = ($result !== false) ? true : $result;
        $this->getResponse()->setResult($result);

        $this->prepareResponse($action, $result);
        $this->getLogger()->response('[%s] %s : %s', [$this->getResponse()->getStatusCode(), $this->getName(), $action]);
        $this->response();
    }
    
    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->getDomainManager()->getModel($this->getName());
    }

    protected function prepareResponse($action, $result)
    {
        $Resource = $this->getResponse()->getData();
        $this->getResponse()->setData($Resource->toArray());
    }

    protected function response()
    {
        echo $this->getResponse()->toJson();
    }

}