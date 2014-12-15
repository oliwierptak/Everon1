<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\FileSystem\CacheLoader;

use Everon\Exception;

class Serialized extends \Everon\FileSystem\CacheLoader
{
    /**
     * @inheritdoc
     */
    public function loadFromCache(\SplFileInfo $CacheFile)
    {
        try {
            return unserialize($this->getFileSystem()->load($CacheFile->getPathname()));            
        }
        catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function saveToCache($name, $cache_data)
    {
        try {
            $CacheFile = new \SplFileInfo($this->cache_directory.pathinfo($name, PATHINFO_BASENAME).'.serialized.php');
            
            if ($CacheFile->isFile() === false) {
                $this->getFileSystem()->save($CacheFile->getPathname(), serialize($cache_data));
            }
        }
        catch (\Exception $e) {
            throw new Exception\FileSystem('Unable to save SerializedCache file for: "%s"', $name, $e);
        }
    }
}