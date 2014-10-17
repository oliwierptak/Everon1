<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Module\Interfaces;

use Everon\Interfaces\FactoryWorker;
use Everon\Http;

/**
 * @method FactoryWorker getFactoryWorker()
 * @method Http\Interfaces\Response getResponse()
 * @method \Everon\Rest\Interfaces\Controller getController($name)
 */
interface Rest extends Module
{
}
