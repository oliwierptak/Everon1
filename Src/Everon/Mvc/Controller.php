<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Mvc;

use Everon\Interfaces;
use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Http;

abstract class Controller extends \Everon\Controller implements Interfaces\MvcController
{
    use Dependency\Injection\DomainManager;
    use Dependency\Injection\Environment;
    use Dependency\Injection\Factory;
    use Dependency\Injection\ViewManager;
    
    use Helper\Arrays;
    use Helper\IsIterable;
    use Helper\String\StartsWith;
    
    protected $CacheLoader = null;


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

        $this->prepareResponse($action, $result);
        $this->getLogger()->response('[%s] %s : %s', [$this->getResponse()->getStatus(), $this->getName(), $action]);
        $this->response();
    }
    
    /**
     * @return Interfaces\View
     */
    public function getView()
    {
        return $this->getViewManager()->getView($this->getName());
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
        if ($result && $this->isCallable($this->getView(), $action)) {
            $this->getView()->{$action}();
        }

        $PageTemplate = $this->getView()->getViewTemplateByAction(
            $action, $this->getViewManager()->getDefaultView()->getContainer()
        );
        
        if ($PageTemplate === null) {
            throw new Http\Exception\NotFound('Page template: "%s/%s" not found', [$this->getName(),$action]);
        }
        /*
        $filename = basename($PageTemplate->getTemplateFile());
        $this->CacheLoader = new \SplFileInfo($this->getEnvironment()->getCache().'view'.DIRECTORY_SEPARATOR.''.$filename);
        $this->CacheLoaderData = new \SplFileInfo($this->getEnvironment()->getCache().'view'.DIRECTORY_SEPARATOR.''.$filename.'.json');
        */
        
        $this->getView()->setContainer($PageTemplate);
        $this->getViewManager()->compileView($action, $this->getView());
        
        $content = (string) $PageTemplate;
        $this->getResponse()->setData($content);
    }

    protected function response()
    {
        echo $this->getResponse()->toHtml();
    }

    /**
     * @inheritdoc
     */
    public function showException(\Exception $Exception, $code=400)
    {
        $ViewManager = $this->getViewManager();
        $View = $ViewManager->getView('Index');
        $View->set('View.error', $Exception->getMessage());
        $Template = $View->getContainer();
        $ViewManager->compileTemplate('View', $Template);
        $this->getResponse()->setData($Template);

        $message = '';
        if ($Exception instanceof Http\Exception) {
            $message = $Exception->getHttpMessage();
            $code = $Exception->getHttpStatus();
        }
        
        $this->getResponse()->setStatus($code);
        $this->getResponse()->setStatusMessage($message);
        $this->response();
    }
}