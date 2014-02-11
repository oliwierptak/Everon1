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

use Everon\Exception;

abstract class Core implements Interfaces\Core
{
    use Dependency\Injection\Logger;
    use Dependency\Injection\Factory;
    use Dependency\Injection\Router;
    use Dependency\Injection\Request;
    use Dependency\Injection\ModuleManager;

    /**
     * @var Interfaces\Controller
     */
    protected $Controller = null;

    /**
     * @var Guid
     */
    protected $Guid = null;
    
    protected $previous_exception_handler = null;

    /**
     * @param $name
     * @param $module
     * @return Interfaces\Controller
     */
    protected abstract function createController($name, $module);
    
    
    protected function runOnce(Guid $Guid)
    {
        if ($this->Guid !== null) {
            return;
        }
        
        register_shutdown_function([$this, 'shutdown']);
        
        $this->Guid = $Guid;
        $this->getLogger()->setGuid($this->Guid->getValue());
        
        $this->previous_exception_handler = set_exception_handler([$this, 'handleExceptions']);
    }
    
    /**
     * @param Guid $Guid
     * @return void
     */
    public function run(Guid $Guid)
    {
        $this->runOnce($Guid);
        /**
         * @var \Everon\Interfaces\MvcController|\Everon\Interfaces\Controller $Controller
         * @var \Everon\Interfaces\ConfigItemRouter $CurrentRoute
         */

        $CurrentRoute = $this->getRouter()->getRouteByRequest($this->getRequest());
        $this->Controller = $this->createController($CurrentRoute->getController(), $CurrentRoute->getModule());
        $this->Controller->execute($CurrentRoute->getAction());
    }
    
    //todo make events, add some kind of profiling class
    public function shutdown()
    {
        $data = $this->getGuid()->getStats();
        $sbs = vsprintf('%0dkb', $data['memory_at_start'] / 1024); 
        $sas = vsprintf('%0dkb', $data['memory_total'] / 1024);
        $mu = vsprintf('%0dkb', ($data['memory_total'] - $data['memory_at_start']) / 1024);
        $time = vsprintf('%.3f', round($data['time'], 3));
        $s = "${time}s $mu $sbs/$sas"; 
        
        $this->getLogger()->monitor($s);
        
        return $s;
    }
    
    protected function restorePreviousExceptionHandler()
    {
        if ($this->previous_exception_handler !== null) {
            restore_exception_handler($this->previous_exception_handler);
        }
    }
    
    public function handleExceptions(\Exception $Exception)
    {
        $this->restorePreviousExceptionHandler();
        $this->getLogger()->critical($Exception);
    }
    
    public function getGuid()
    {
        return $this->Guid;
    }
    
}
