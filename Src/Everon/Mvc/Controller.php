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
use Everon\Http;

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

    protected function prepareResponse($action)
    {
        if ($this->isCallable($this->getView(), $action)) {
            $this->getView()->{$action}();
        }

        $PageTemplate = $this->getView()->getViewTemplateByAction($action);
        $PageTemplate = $PageTemplate ?: $this->getViewManager()->getDefaultView()->getViewTemplate();

        if ($PageTemplate === null) {
            throw new Http\Exception\NotFound('Page: "%s/%s" not found', [$this->getName(),$action]);
        }

        $this->getViewManager()->compileTemplate($PageTemplate);
        $this->getResponse()->setData((string) $PageTemplate);
    }

    protected function response()
    {
        $this->getResponse()->send();
        echo $this->getResponse()->toHtml();
    }

}