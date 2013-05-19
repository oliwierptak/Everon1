<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

use Everon\Exception;
use Everon\Interfaces;

interface ConfigLoader
{
    function getConfigDirectory();
    function getCacheDirectory();
    
    /**
     * @param \Closure $Compiler
     * @param $use_cache
     * @param $default_config_filename
     * @return array
     */
    function load(\Closure $Compiler, $use_cache, $default_config_filename);

    /**
     * @param $filename
     * @return array|null
     */
    function read($filename);
        
    /**
     * @param Interfaces\Config $Config
     * @throws Exception\Config
     */
    function saveConfigToCache(Interfaces\Config $Config);
}
