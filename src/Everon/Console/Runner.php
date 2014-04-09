<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Console;

use Everon\Dependency;
use Everon\Exception;
use Everon\Interfaces;
use Everon\RequestIdentifier;

class Runner extends \Everon\Core
{
    /**
     * @inheritdoc
     */
    public function run(RequestIdentifier $RequestIdentifier)
    {
        $this->runOnce($RequestIdentifier);
        if ($this->getRequest()->isEmptyUrl()) {
            echo 'empty url';
        }
        else {
            
        }
    }
}
