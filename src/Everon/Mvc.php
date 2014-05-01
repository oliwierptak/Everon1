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
            $this->showException($NotFound, $this->Controller);
        }
        catch (Exception\InvalidRoute $Exception) {
            $NotFound = new Http\Exception((new Http\Message\NotFound($Exception->getMessage())));
            $this->showException($NotFound, $this->Controller);
        }
        catch (Http\Exception $Exception) {
            $this->showException($Exception, $this->Controller);
        }
        catch (\Exception $Exception) {
            $Internal = new Http\Exception((new Http\Message\InternalServerError($Exception->getMessage())));
            $this->showException($Internal, $this->Controller);
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

}
