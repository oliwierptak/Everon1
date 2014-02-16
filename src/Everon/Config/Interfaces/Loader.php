<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config\Interfaces;

use Everon\Exception;
use Everon\Interfaces;

interface Loader
{
    function getConfigDirectory();
    function getCacheDirectory();
    
    /**
     * @param $use_cache
     * @return array
     */
    function load($use_cache);

    /**
     * @param \SplFileInfo $ConfigFile
     * @param $use_cache
     * @return LoaderItem
     */
    function loadByFile(\SplFileInfo $ConfigFile, $use_cache);

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
