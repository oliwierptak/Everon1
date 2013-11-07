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
use Everon\Http;

class Mvc extends Core implements Interfaces\Core
{
    protected function createController($name)
    {
        return $this->getFactory()->buildController($name, 'Everon\Mvc\Controller');
    }

    /**
     * @param Guid $Guid
     * @return void
     */
    public function run(Guid $Guid)
    {
        try {
            parent::run($Guid);
        }
        catch (Http\Exception\NotFound $e) {
            $this->getLogger()->notFound($e);
            echo $e->getMessage()."\n";
        }
        catch (Exception $e) {
            $this->getLogger()->error($e);
            echo "Error: ".$e->getMessage()."\n";
        }        
    }

    public function shutdown()
    {
        $s = parent::shutdown();
        echo "<hr><pre>$s</pre>";
    }
}
