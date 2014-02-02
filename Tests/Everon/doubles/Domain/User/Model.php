<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Domain\User;

use Everon\Dependency;

class Model
{
    use Dependency\Injection\Logger;

    public function testOne()
    {
        $this->getLogger()->debug('some debug');
    }
}