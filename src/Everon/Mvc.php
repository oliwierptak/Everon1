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
use Everon\Exception;
use Everon\Http;
use Everon\Http\Exception as HttpException;

class Mvc extends Core implements Interfaces\Core
{
    use Dependency\Injection\ViewManager;
    
    /**
     * @var Interfaces\MvcController
     */
    protected $Controller = null;
    
    /**
     * @param $name
     * @return Interfaces\MvcController
     */
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
        catch (Exception\RouteNotDefined $Exception) {
            $this->getLogger()->error($Exception);
            $NotFound = new HttpException\NotFound('Page not found: '.$Exception->getMessage());
            $this->showControllerException($NotFound->getHttpStatus(), $NotFound, $this->Controller);
        }
        catch (Exception $Exception) {
            $this->getLogger()->error($Exception);
            $this->showControllerException(400, $Exception, $this->Controller);
        }
    }

    /**
     * @param $code
     * @param \Exception $Exception
     * @param Interfaces\MvcController|null $Controller
     */
    public function showControllerException($code, \Exception $Exception, $Controller)
    {
        /**
         * @var Interfaces\MvcController $Controller
         */
        if (isset($Controller) === false) {
            $Controller = $this->createController('Error');
        }
        
        $Controller->showException($Exception, $code);
    }

    public function shutdown()
    {
        $s = parent::shutdown();
        echo "<hr><pre>$s</pre>";
    }
}
