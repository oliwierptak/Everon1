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

class Router extends \Everon\Config implements Interfaces\Config
{      
    protected function buildItem($name, array $data)
    {
        /*$data['url'] = '%application.e4nv.url%'.$data['url'];
        $data = $this->recompile($data);*/
        $data[Item\Router::PROPERTY_MODULE] = '_Core';
        
        return $this->getFactory()->buildConfigItemRouter($name, $data);
    }
}
