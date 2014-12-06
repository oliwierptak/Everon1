<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\FileSystem\Interfaces;

use Everon\Exception;

interface CacheLoader extends \Everon\Interfaces\Dependency\Factory, \Everon\Interfaces\Dependency\FileSystem
{
    /**
     * @return string
     */
    function getCacheDirectory();

    /**
     * @param $cache_directory
     */
    function setCacheDirectory($cache_directory);

    /**
     * @param $name
     * @return bool
     */
    function cacheFileExists($name);

    /**
     * @param $name
     * @return \SplFileInfo
     */
    function generateCacheFileByName($name);

    /**
     * @inheritdoc
     */
    function load();

    /**
     * @param \SplFileInfo $CacheFile
     * @return mixed|null
     */
    function loadFromCache(\SplFileInfo $CacheFile);

    /**
     * @param $name
     * @param mixed $cache_data
     */
    function saveToCache($name, $cache_data);
}