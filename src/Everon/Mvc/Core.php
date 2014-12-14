<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Mvc;

use Everon\RequestIdentifier;
use Everon\Exception;
use Everon\Helper;
use Everon\Http;


/**
 * @method \Everon\Http\Interfaces\Response getResponse
 * @method \Everon\Mvc\Interfaces\Controller getController
 * @property \Everon\Mvc\Interfaces\Controller $Controller
 */
class Core extends \Everon\Http\Core implements Interfaces\Core
{
    /**
     * @inheritdoc
     */
    public function run(RequestIdentifier $RequestIdentifier)
    {
        try {
            parent::run($RequestIdentifier);
        }
        catch (Exception\RouteNotDefined $Exception) {
            $NotFound = new Http\Exception((new Http\Message\NotFound('Page not found: '.$Exception->getMessage())));
            $this->showException($NotFound, $this->Controller);
        }
        catch (Exception\InvalidRoute $Exception) {
            $BadRequest = new Http\Exception((new Http\Message\BadRequest($Exception->getMessage())));
            $this->showException($BadRequest, $this->Controller);
        }
        catch (\Exception $Exception) {
            $Internal = new Http\Exception((new Http\Message\InternalServerError($Exception->getMessage())));
            $this->showException($Internal, $this->Controller);
        }
    }
}
