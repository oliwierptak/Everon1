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

use Everon\Dependency;
use Everon\Rest\Dependency as RestDependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Http;
use Everon\Module;

/**
 * @method Http\Interfaces\Response getResponse()
 * @method Interfaces\Request getRequest()
 * @method Module\Interfaces\Rest getModule()
 */
abstract class Controller extends \Everon\Controller implements Interfaces\Controller
{
    use Dependency\Injection\DomainManager;
    use Dependency\Injection\Environment;
    use Dependency\Injection\Factory;
    use Dependency\Injection\ModuleManager;
    use RestDependency\Injection\ResourceManager;

    
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
        if ($Resource instanceof Interfaces\ResourceBasic) {
            $Resource = $Resource->toArray();
            $this->getResponse()->setData($Resource);
        }
    }

    protected function response()
    {
        echo $this->getResponse()->toJson();
    }

    /**
     * @inheritdoc
     */
    public function addResourceFromRequest()
    {
        $version = $this->getRequest()->getVersion();
        $data = $this->getRequest()->getPostCollection()->toArray(true);
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $Resource = $this->getResourceManager()->add($version, $resource_name, $data);
        
        $this->getResponse()->setData($Resource);
        $this->getResponse()->setStatusCode(201);
        $this->getResponse()->setHeader('Location', $Resource->getHref());
    }

    /**
     * @inheritdoc
     */
    public function saveResourceFromRequest()
    {
        $version = $this->getRequest()->getVersion();
        $data = $this->getRequest()->getPostCollection()->toArray(true);
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $resource_id = $this->getRequest()->getQueryParameter('resource_id', null);
        $Resource = $this->getResourceManager()->save($version, $resource_name, $resource_id, $data);

        $this->getResponse()->setData($Resource);
        $this->getResponse()->setStatusCode(200);
    }

    /**
     * @inheritdoc
     */
    public function deleteResourceFromRequest()
    {
        $version = $this->getRequest()->getVersion();
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $resource_id = $this->getRequest()->getQueryParameter('resource_id', null);
        $Resource = $this->getResourceManager()->delete($version, $resource_name, $resource_id);

        $this->getResponse()->setData($Resource);
        $this->getResponse()->setStatusCode(204);
    }
    
    /**
     * @inheritdoc
     */
    public function serveResourceFromRequest()
    {
        $Resource = $this->getResourceFromRequest();
        $this->getResponse()->setData($Resource);
    }
    
    /**
     * @inheritdoc
     */
    public function getResourceFromRequest()
    {
        $version = $this->getRequest()->getVersion();
        $resource_id = $this->getRequest()->getQueryParameter('resource_id', null);
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $Navigator = $this->getFactory()->buildRestResourceNavigator($this->getRequest());

        if ($resource_id === null) {
            $Resource = $this->getResourceManager()->getCollectionResource($resource_name, $version, $Navigator);
        }
        else {
            $Resource = $this->getResourceManager()->getResource($resource_id, $resource_name, $version, $Navigator);
        }

        return $Resource;
    }
}