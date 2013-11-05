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

use Everon\Interfaces;
use Everon\Exception;

class Mvc extends Core implements Interfaces\Core
{
    protected function createController($name)
    {
        return $this->getFactory()->buildController($name, 'Everon\Mvc\Controller');
    }

    //todo make events
    public function shutdown()
    {
        parent::shutdown();
        $m = vsprintf('%04d kb', (memory_get_usage(true)/1024));
        $mp = vsprintf('%04d kb', (memory_get_peak_usage(true)/1024));

        $s = vsprintf('%.3f', round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3)) .
            "s $m / $mp";

        echo "<hr><pre>$s</pre>";

    }
}
