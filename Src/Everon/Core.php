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
    use Dependency\Injection\Router;
    use Dependency\Injection\Factory;
    use Dependency\Injection\Response;
    
    protected abstract function runMe();
    

    /**
     * @return Interfaces\Controller|null
     * @throws Exception\InvalidRouterParameter|\Exception
     */
    public function run()
    {
        try {
            register_shutdown_function(array($this, 'shutdown'));
            $this->runMe();
        }
        catch (Exception\InvalidRouterParameter $e) {
            //todo: raise event for form validation
            throw $e;
        }
        catch (Exception\PageNotFound $e) {
            $this->getLogger()->error($e);
            echo "Unknown command: ".$e->getMessage()."\n";
        }
        catch (Exception\DomainException $e) {
            $this->getLogger()->error($e);
            echo "Domain error: ".$e->getMessage()."\n";
        }
        catch (Exception $e) {
            $this->getLogger()->error($e);
            echo "Error: ".$e->getMessage()."\n";
        }

        return null;
    }
    
    public function shutdown()
    {
        //$this->getLogger()->trace('shutting down');
    }
    
}
