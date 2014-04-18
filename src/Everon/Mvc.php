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

/**
 * @method \Everon\Http\Interfaces\Response getResponse
 */
class Mvc extends Core implements Interfaces\Core
{
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Response;
    
    /**
     * @var Mvc\Interfaces\Controller
     */
    protected $Controller = null;

    /**
     * @inheritdoc
     */
    public function run(RequestIdentifier $RequestIdentifier)
    {
        try {
            parent::run($RequestIdentifier);
        }
        catch (Exception\RouteNotDefined $Exception) {
            $NotFound = new Http\Exception((new Http\Message\NotFound('Page not found')));
            $this->showException($NotFound->getHttpMessage()->getStatus(), $NotFound, $this->Controller);
        }
        catch (Exception $Exception) {
            $NotFound = new Http\Exception((new Http\Message\InternalServerError($Exception->getMessage())));
            $this->showException($NotFound->getHttpMessage()->getStatus(), $NotFound, $this->Controller);
        }
        finally {
            $this->getLogger()->mvc(
                sprintf(
                    '[%d] %s %s (%s)',
                    $this->getResponse()->getStatusCode(), $this->getRequest()->getMethod(), $this->getRequest()->getPath(), $this->getResponse()->getStatusMessage()
                )
            );
        }
    }

    /**
     * @param $code
     * @param \Exception $Exception
     * @param Mvc\Interfaces\Controller|null $Controller
     */
    public function showException($code, \Exception $Exception, $Controller)
    {
        /**
         * @var Mvc\Interfaces\Controller $Controller
         */
        if ($Controller === null) {
            $Controller = $this->getModuleManager()->getModule('Mvc')->getController('Error');
        }
        
        $Controller->showException($Exception, $code);
    }
}
