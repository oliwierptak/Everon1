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
            $NotFound = new Http\Exception((new Http\Message\NotFound('Invalid resource name or version')));
            $this->showException($NotFound->getHttpMessage()->getStatus(), $NotFound, $this->Controller);
        }
        catch (\Exception $Exception) {
            $this->showException(500, $Exception, $this->Controller);
        }
        finally {
            $this->getLogger()->mvc(
                sprintf(
                    '[%d] %s %s (%s)',
                    $this->getResponse()->getStatusCode(), $this->getRequest()->getMethod(), $this->getRequest()->getPath(), $this->getResponse()->getStatusMessage()
                )
            );
        }/*
        catch (Exception\RouteNotDefined $Exception) {
            $this->getLogger()->error($Exception);
            $NotFound = new Http\Exception\NotFound('Page not found: '.$Exception->getMessage());
            $this->showControllerException($NotFound->getHttpStatus(), $NotFound, $this->Controller);
        }
        catch (\Exception $Exception) {
            $this->getLogger()->error($Exception);
            $this->showControllerException(400, $Exception, $this->Controller);
        }*/
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
