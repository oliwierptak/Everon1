<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test;

class MyController extends \Everon\Mvc\Controller implements \Everon\Interfaces\Controller, \Everon\Interfaces\MvcController
{
    public function testOne()
    {
        $this->getView()->setOutput('test one');
    }
}