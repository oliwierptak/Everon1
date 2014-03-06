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
     * @var Interfaces\Module
     */
    protected $Module = null;

    /**
     * @var RequestIdentifier
     */
    protected $RequestIdentifier = null;
    
    protected $previous_exception_handler = null;

    protected function runOnce(RequestIdentifier $RequestIdentifier)
    {
        if ($this->RequestIdentifier !== null) {
            return;
        }
        
        register_shutdown_function([$this, 'shutdown']);
        
        $this->RequestIdentifier = $RequestIdentifier;
        $this->getLogger()->setRequestIdentifier($this->RequestIdentifier->getValue());
        
        $this->previous_exception_handler = set_exception_handler([$this, 'handleExceptions']);
    }
    
    /**
     * @param RequestIdentifier $RequestIdentifier
     * @return void
     * @throws Exception\Core
     */
    public function run(RequestIdentifier $RequestIdentifier)
    {
        $this->runOnce($RequestIdentifier);
        
        if ($this->getRequest()->isEmptyUrl()) {
            $this->Module = $this->getModuleManager()->getDefaultModule();
            
            if ($this->Module === null) {
                throw new Exception\Core('Default module not defined');
            }
            
            $this->Module->setup();
            $CurrentRoute = $this->Module->getRouterConfig()->getDefaultItem();
            $this->getRouter()->validateAndUpdateRequest($CurrentRoute, $this->getRequest());
        }
        else {
            $CurrentRoute = $this->getRouter()->getRouteByRequest($this->getRequest());
            $this->Module = $this->getModuleManager()->getModule($CurrentRoute->getModule());

            if ($this->Module === null) {
                throw new Exception\Core('No module defined for this request');
            }
            
            $this->Module->setup();
        }

        $this->Controller = $this->Module->getController($CurrentRoute->getController());
        $this->Controller->execute($CurrentRoute->getAction());
    }
    
    //todo make events, add some kind of profiling class
    public function shutdown()
    {
        $data = $this->getRequestIdentifier()->getStats();
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
    
    public function getRequestIdentifier()
    {
        return $this->RequestIdentifier;
    }
    
}
