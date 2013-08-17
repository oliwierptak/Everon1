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
     * @param $result
     * @param Interfaces\Response $Response
     * @return Interfaces\Response
     */
    public function result($result, Interfaces\Response $Response)
    {
        $Response->setResult($result);
        
        if ($result === false) {
            $data = vsprintf('Invalid response for route: "%s"', [$this->getRouter()->getCurrentRoute()->getName()]);
            $Response->setData($data);
        }
        else {
            $Response->setData($this->getView()->getOutput());
        }

        $Response->send();
        echo $Response->toHtml();
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
        return $this->getModelManager()->getModel($this->getName());
    }

}