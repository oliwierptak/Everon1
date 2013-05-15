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

use Everon\Interfaces;

class Router extends \Everon\Config implements Interfaces\Config
{
    protected function buildItem(array $config_data)
    {
        return $this->getFactory()->buildConfigItemRouter($config_data);
    }

}
