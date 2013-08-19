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
    
    protected function buildItem($name, array $config_data)
    {
        if ($this->getCompiler() instanceof \Closure) {
            $config_data['url'] = '%application.url%'.$config_data['url'];
            $this->getCompiler()->__invoke($config_data);
        }
        
        return $this->getFactory()->buildConfigItemRouter($name, $config_data);
    }

}
