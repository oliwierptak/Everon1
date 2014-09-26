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
use Everon\Domain;
use Everon\Rest\Dependency as RestDependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Http;

/**
 * @method Http\Interfaces\Response getResponse()
 * @method Interfaces\Request getRequest()
 * @method \Everon\Module\Interfaces\Rest getModule()
 */
abstract class Controller extends \Everon\Controller implements Interfaces\Controller
{
    use Domain\Dependency\Injection\DomainManager;
    use Dependency\Injection\Factory;
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
        $this->response();
    }
    
    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->getDomainManager()->getModelByName($this->getName());
    }
    
    protected function prepareResponse($action, $result)
    {
        $Resource = $this->getResponse()->getData();
        if ($Resource instanceof Interfaces\ResourceBasic) {
            $Resource = $Resource->toArray();
            $this->getResponse()->setData($Resource);
        }

        if ($this->getResponse()->wasStatusSet() === false) {//DRY
            $Ok = new Http\Message\Ok();
            $this->getResponse()->setStatusCode($Ok->getCode());
            $this->getResponse()->setStatusMessage($Ok->getMessage());
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
        $user_id = 1;
        $version = $this->getRequest()->getVersion();
        $data = $this->getRequest()->getPostCollection()->toArray(true);
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $Resource = $this->getResourceManager()->add($version, $resource_name, $data, $user_id);
        
        $this->getResponse()->setData($Resource);
        $this->getResponse()->setStatusCode(201);
        $this->getResponse()->setHeader('Location', $Resource->getHref()->getUrl());
    }

    /**
     * @inheritdoc
     */
    public function saveResourceFromRequest()
    {
        $user_id = 1;
        $version = $this->getRequest()->getVersion();
        $data = $this->getRequest()->getPostCollection()->toArray(true);
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $resource_id = $this->getRequest()->getQueryParameter('resource_id', null);
        $Resource = $this->getResourceManager()->save($version, $resource_name, $resource_id, $data, $user_id);

        $this->getResponse()->setData($Resource);
        $this->getResponse()->setStatusCode(200);
    }

    /**
     * @inheritdoc
     */
    public function deleteResourceFromRequest()
    {
        $user_id = 1;
        $version = $this->getRequest()->getVersion();
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $resource_id = $this->getRequest()->getQueryParameter('resource_id', null);
        $Resource = $this->getResourceManager()->delete($version, $resource_name, $resource_id, $user_id);

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
    
    public function serveCollectionItemFromRequest()
    {
        $version = $this->getRequest()->getVersion();
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $resource_id = $this->getRequest()->getQueryParameter('resource_id', null);
        $collection = $this->getRequest()->getQueryParameter('collection', null);
        $item_id = $this->getRequest()->getQueryParameter('item_id', null);
        $Navigator = $this->getFactory()->buildRestResourceNavigator($this->getRequest());
        $Resource = $this->getResourceManager()->getCollectionItemResource($version, $resource_name, $resource_id, $collection, $item_id, $Navigator);

        $this->getResponse()->setData($Resource);
        $this->getResponse()->setStatusCode(200);
    }

    /**
     * @inheritdoc
     */
    public function addResourceCollectionFromRequest()
    {
        $user_id = 1;
        $version = $this->getRequest()->getVersion();
        $data = $this->getRequest()->getPostCollection()->toArray(true);
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $resource_id = $this->getRequest()->getQueryParameter('resource_id', null);
        $collection = $this->getRequest()->getQueryParameter('collection', null);
        $Resource = $this->getResourceManager()->addCollection($version, $resource_name, $resource_id, $collection, $data, $user_id);

        $this->getResponse()->setData($Resource);
        $this->getResponse()->setStatusCode(201);
        $this->getResponse()->setHeader('Location', $Resource->getHref()->getUrl());
    }

    /**
     * @inheritdoc
     */
    public function saveResourceCollectionFromRequest()
    {
        $user_id = 1;
        $version = $this->getRequest()->getVersion();
        $data = $this->getRequest()->getPostCollection()->toArray(true);
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $resource_id = $this->getRequest()->getQueryParameter('resource_id', null);
        $collection = $this->getRequest()->getQueryParameter('collection', null);
        $Resource = $this->getResourceManager()->saveCollection($version, $resource_name, $resource_id, $collection, $data, $user_id);

        $this->getResponse()->setData($Resource);
        $this->getResponse()->setStatusCode(200);
    }

    /**
     * @inheritdoc
     */
    public function deleteResourceCollectionFromRequest()
    {
        $user_id = 1;
        $version = $this->getRequest()->getVersion();
        $data = $this->getRequest()->getPostCollection()->toArray(true);
        $resource_name = $this->getRequest()->getQueryParameter('resource', null);
        $resource_id = $this->getRequest()->getQueryParameter('resource_id', null);
        $collection = $this->getRequest()->getQueryParameter('collection', null);
        $Resource = $this->getResourceManager()->deleteCollection($version, $resource_name, $resource_id, $collection, $data, $user_id);

        $this->getResponse()->setData($Resource);
        $this->getResponse()->setStatusCode(204);
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
            $Resource = $this->getResourceManager()->getCollectionResource($version, $resource_name, $Navigator);
        }
        else {
            $Resource = $this->getResourceManager()->getResource($version, $resource_name, $resource_id, $Navigator);
        }

        return $Resource;
    }

    /**
     * @inheritdoc
     */
    public function showException(\Exception $Exception)
    {
        $message = $Exception->getMessage();
        $code = $Exception->getCode();
        if ($Exception instanceof Http\Exception) {
            $code = $Exception->getHttpMessage()->getCode();
        }

        $this->getResponse()->setData(['error' => $message]); //xxx

        $this->getResponse()->setStatusCode($code);
        $this->getResponse()->setStatusMessage($message);
        $this->response();
    }
}