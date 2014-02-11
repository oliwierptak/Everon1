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
    /**
     * @param $name
     * @param $module
     * @return Interfaces\Controller
     */
    protected function createController($name, $module)
    {
        return $this->getFactory()->buildController($name, 'Everon\Console\Controller');
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
        catch (Exception\RouteNotDefined $e) {
            $this->getLogger()->notFound($e);
            echo "Unknown command: ".$e->getMessage()."\n";
        }
        catch (Exception $e) {
            $this->getLogger()->error($e);
            echo "Error: ".$e->getMessage()."\n";
        }
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
