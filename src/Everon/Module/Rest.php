<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Module;

use Everon\Dependency;
use Everon\Rest\Dependency as RestDependency;
use Everon\Http;
use Everon\Interfaces\FactoryWorker;

/**
 * @method FactoryWorker getFactoryWorker()
 * @method Http\Interfaces\Response getResponse()
 * @method \Everon\Rest\Interfaces\Controller getController($name)
 */
abstract class Rest extends \Everon\Module\AbstractModule implements Interfaces\Rest
{
    public function setup()
    {

    }
}