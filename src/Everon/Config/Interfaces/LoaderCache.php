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

interface LoaderCache extends Interfaces\Dependency\Factory, Interfaces\Dependency\FileSystem
{
    function getCacheDirectory();

    /**
     * @param $cache_directory
     */
    function setCacheDirectory($cache_directory);

    /**
     * @return array
     */
    function load();

    /**
     * @param \SplFileInfo $ConfigFile
     * @return LoaderItem
     * @throws Exception\Config
     */
    function loadFromCache(\SplFileInfo $ConfigFile);

    /**
     * @param Interfaces\Config $Config
     * @throws Exception\Config
     */
    function saveConfigToCache(Interfaces\Config $Config);
}
