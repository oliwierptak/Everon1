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

abstract class Core implements Interfaces\Core
{
    use Dependency\Injection\Logger;
    use Dependency\Injection\Factory;
    use Dependency\Injection\Router;
    use Dependency\Injection\Request;

    /**
     * @var Interfaces\Controller
     */
    protected $Controller = null;

    /**
     * @var Guid
     */
    protected $Guid = null;
    
    
    protected abstract function createController($name);
    
    
    protected function runOnce(Guid $Guid)
    {
        if ($this->Guid !== null) {
            return;
        }

        register_shutdown_function(array($this, 'shutdown'));
        
        $this->Guid = $Guid;

        $this->getLogger()->setGuid($this->Guid->getValue()); //todo: should this pass session/request object of some kind?

/*        set_exception_handler(function ($Exception) {
            $this->getLogger()->critical($Exception);
        });*/
    }
    
    /**
     * @param Guid $Guid
     * @return void
     */
    public function run(Guid $Guid)
    {
        try {
            $this->runOnce($Guid);

            /**
             * @var \Everon\Interfaces\MvcController|\Everon\Interfaces\Controller $Controller
             * @var \Everon\Interfaces\ConfigItemRouter $CurrentRoute
             */
            $CurrentRoute = $this->getRouter()->getRouteByRequest($this->getRequest());
            $controller_name = $CurrentRoute->getController();
            $action = $CurrentRoute->getAction();

            $Controller = $this->createController($controller_name);
            $this->Controller = $Controller;
            $Controller->execute($action);
        }
        catch (Exception\InvalidRouterParameter $e) {
            //todo: raise event for form validation
            $this->getLogger()->warn($e);
            echo $e->getMessage()."\n";
        }
        catch (Exception\PageNotFound $e) {
            $this->getLogger()->notFound($e);
            echo "Unknown command: ".$e->getMessage()."\n";
        }
        catch (Exception $e) {
            $this->getLogger()->error($e);
            echo "Error: ".$e->getMessage()."\n";
        }
    }
    
    //todo make events, add some kind of profiling class
    public function shutdown()
    {
        $m = vsprintf('%04d kb', (memory_get_usage(true)/1024));
        $mp = vsprintf('%04d kb', (memory_get_peak_usage(true)/1024));
        
        $s = vsprintf('%.3f', round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3)) .
            "s $m / $mp";

        $this->getLogger()->monitor($s);
    }
    
    public function getGuid()
    {
        return $this->Guid;
    }
    
}
