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
    use Dependency\Injection\ModuleManager;
    use Dependency\Injection\ViewManager;
    
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
        return $this->getModule()->getView($this->getName());
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

        $ActionTemplate = $this->getView()->getTemplate($action, $this->getView()->getData());
        if ($ActionTemplate === null) { //apparently no template was used, fall back to string
            $ActionTemplate = $this->getView()->getContainer();
        }

        $Theme = $this->getViewManager()->getCurrentTheme();
        $Theme->set('View.body', $ActionTemplate);
        $data = $this->arrayMergeDefault($Theme->getData(), $ActionTemplate->getData());
        $Theme->setData($data);
        $this->getView()->setContainer($Theme->getContainer());
        $this->getViewManager()->compileView($action, $this->getView());

        $content = (string) $this->getView()->getContainer();
        $this->getResponse()->setData($content);
    }

    protected function response()
    {
        echo $this->getResponse()->toHtml();
    }
}