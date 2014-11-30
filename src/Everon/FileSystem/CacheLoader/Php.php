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

class Php extends \Everon\FileSystem\CacheLoader
{
    /**
     * @inheritdoc
     */
    public function loadFromCache(\SplFileInfo $CacheFile)
    {
        $filename = $CacheFile->getPathname();
        $cache = null;
        include($filename);
        return $cache;  
    }

    /**
     * @inheritdoc
     */
    public function saveToCache($name, $cache_data)
    {
        try {
            $CacheFile = new \SplFileInfo($this->cache_directory.pathinfo($name, PATHINFO_BASENAME).'.exported.php');
            
            if ($CacheFile->isFile() === false) {
                $data = var_export($cache_data, true);
                $this->getFileSystem()->save($CacheFile->getPathname(), "<?php \$cache = $data; ");
            }
        }
        catch (\Exception $e) {
            throw new Exception\FileSystem('Unable to save PhpCache file for: "%s"', $name, $e);
        }
    }
}