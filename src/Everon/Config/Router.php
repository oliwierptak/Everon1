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

class Router extends \Everon\Config
{
    public function buildItem($name, array $data)
    {
        //$data['url'] = '%application.server.url%'.$data['url']; //auto append application url
        //$data = $this->recompile($data);
        return $this->getFactory()->buildConfigItem($name, $data, 'Everon\Config\Item\Router');
    }
}
