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
        $this->setOutputFromAction($action);
        parent::execute($action);
    }

    /**
     * @param $action
     * @param array $data
     */
    public function setOutputFromAction($action)
    {
        $Filename = $this->getViewManager()->getTemplateFilename($action);
        if ($Filename->isFile()) {
            $Output = $this->getViewManager()->getTemplate($Filename, $data);
        }
    }

    protected function prepareResponse()
    {
        $this->getResponse()->setData((string) $this->getView());
    }

    protected function response()
    {
        $this->getResponse()->send();
        echo $this->getResponse()->toHtml();
    }

}