<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Core\Mvc;

use Everon\Interfaces;
use Everon\Dependency;
use Everon\Exception;

abstract class Controller extends \Everon\Controller implements Interfaces\Controller, Interfaces\MvcController
{
    use Dependency\Injection\ViewManager;
    use Dependency\Injection\ModelManager;

    
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
        return $this->getModelManager()->getModel($this->getName());
    }

    /**
     * @param $action
     * @return void
     * @throws Exception\InvalidControllerMethod
     */
    public function execute($action)
    {
        $this->getView()->setOutputFromAction($action, $this->getView()->getData());
        parent::execute($action);
    }

    protected function prepareResponse()
    {
        $this->getResponse()->setData($this->getView()->getOutput());
    }

    protected function response()
    {
        $this->getResponse()->send();
        echo $this->getResponse()->toHtml();
    }

}