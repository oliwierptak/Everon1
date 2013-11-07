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
        $CurrentView = $this->getView();
        if ($this->isCallable($CurrentView, $action)) {
            $CurrentView->{$action}();
        }

        $DefaultView = $this->getViewManager()->getDefaultView();
        $CompiledView = $this->compileView($action, $DefaultView);
        $this->getResponse()->setData((string) $CompiledView);
    }

    /**
     * @param $action
     * @param Interfaces\View $DefaultView
     * @return Interfaces\View
     */
    protected function compileView($action, Interfaces\View $DefaultView)
    {
        $ActionTemplate = $this->getView()->getTemplate($action, $this->getView()->getData());
        $ViewTemplate = $this->getView()->getViewTemplate() ?: $DefaultView->getViewTemplate();
        
        $ViewTemplate->set('View.Main', $ActionTemplate);
        $this->getViewManager()->compileTemplate($ViewTemplate);
        
        return $ViewTemplate;
    }

    protected function response()
    {
        $this->getResponse()->send();
        echo $this->getResponse()->toHtml();
    }

}