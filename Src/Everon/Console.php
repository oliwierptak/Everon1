<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

use Everon\Dependency;
use Everon\Interfaces;

class Console extends Core implements Interfaces\Core
{
    protected function createController($name)
    {
        return $this->getFactory()->buildController($name, 'Everon\Console\Controller');
    }

    public function handleExceptions(\Exception $Exception)
    {
        $this->restorePreviousExceptionHandler();
        $this->getLogger()->console($Exception);
        echo $Exception;
    }
    
    public function shutdown()
    {
        $s = parent::shutdown();
        echo "\nExecuted in $s\n";
    }    
}
