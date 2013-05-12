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

class MyController extends \Everon\Controller
{
    public function beforeTestOne()
    {
        $this->setOutput('before test one');
    }

    public function testOne()
    {
        $this->setOutput('test one');
    }

    public function afterTestOne()
    {
        $this->setOutput('after test one');
    }

}