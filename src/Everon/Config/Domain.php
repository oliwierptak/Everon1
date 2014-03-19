<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config;

use Everon\Dependency;
use Everon\Interfaces;

class Domain extends \Everon\Config
{      
    protected function buildItem($name, array $data)
    {
        return $this->getFactory()->buildConfigItemDomain($name, $data);
    }
}

