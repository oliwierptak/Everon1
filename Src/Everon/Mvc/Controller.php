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

        $data = $CurrentView->getData();
        $CurrentTemplate = $CurrentView->getTemplate($action, $data);

        $DefaultView = $this->getViewManager()->getDefaultView();
        $ViewTemplate = $CurrentView->getViewTemplate() ?: $DefaultView->getViewTemplate();
        
        $ViewTemplate->set('Main', $CurrentTemplate);

        $this->getResponse()->setData($ViewTemplate);
    }

    protected function response()
    {
        $this->getResponse()->send();
        echo $this->getResponse()->toHtml();
    }

}