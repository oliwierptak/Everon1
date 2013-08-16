<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Core;

use Everon\Interfaces;
use Everon\Exception;

class Mvc extends \Everon\Core implements Interfaces\Core
{
    protected function runMe()
    {
        /**
         * @var \Everon\Interfaces\MvcController|\Everon\Interfaces\Controller $Controller
         * @var \Everon\Interfaces\ConfigItemRouter $CurrentRoute
         */
        $CurrentRoute = $this->getRouter()->getCurrentRoute();
        $controller_name = $CurrentRoute->getController();
        $action = $CurrentRoute->getAction();

        $Controller = $this->getFactory()->buildController($controller_name);
        $this->getLogger()->actions('Executing: %s', $action);
        
        $Controller->execute($action);

        $Controller->getView()->setOutputFromAction($action, $Controller->getView()->getData());
        
        $result = $Controller->getView()->getOutput();
        $Controller->result($result, $this->getResponse());
    }

}
