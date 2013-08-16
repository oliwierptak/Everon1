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

use Everon\Dependency;
use Everon\Interfaces;

class Console extends \Everon\Core implements Interfaces\Core
{
    use Dependency\Injection\Request;
    
    public function runMe()
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
        
        $this->writeln($action);
    }
    
    protected function writeln($line)
    {
        echo "${line}\n";
    }
    
}
