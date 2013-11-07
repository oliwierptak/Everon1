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
        $s = parent::shutdown();
        echo "<hr><pre>$s</pre>";
    }
}
