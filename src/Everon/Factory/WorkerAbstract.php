<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Factory;

use Everon\Application;
use Everon\Helper;
use Everon\Dependency;
use Everon\Interfaces;


abstract class WorkerAbstract implements Interfaces\FactoryWorker
{
    use Dependency\Factory;
    
    public function __construct(Application\Interfaces\Factory $Factory)
    {
        $this->Factory = $Factory;
    }
}