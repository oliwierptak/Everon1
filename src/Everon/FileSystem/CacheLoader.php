<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\FileSystem;

use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;

abstract class CacheLoader implements Interfaces\CacheLoader
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\FileSystem;
    
    use Helper\Arrays;
    use Helper\String\LastTokenToName;

    /**
     * @var string
     */
    protected $type = null;

    /**
     * @param string
     */
    protected $cache_directory = null;


    /**
     * @inheritdoc
     */
    abstract public function loadFromCache(\SplFileInfo $CacheFile);

    /**
     * @inheritdoc
     */
    abstract public function saveToCache($name, $cache_data);
    

    /**
     * @param $cache_directory
     */
    public function __construct($cache_directory)
    {
        $this->cache_directory = $cache_directory;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        if ($this->type === null) {
            $this->type = $this->stringLastTokenToName(get_class($this));
        }
        
        return $this->type;
    }
    
    /**
     * @inheritdoc
     */
    public function getCacheDirectory()
    {
        return $this->cache_directory;
    }

    /**
     * @inheritdoc
     */
    public function setCacheDirectory($cache_directory)
    {
        $this->cache_directory = $cache_directory;
    }

    /**
     * @param $name
     * @return bool
     */
    public function cacheFileExists($name)
    {
        $CacheFile = $this->generateCacheFileByName($name);
     //   sd($CacheFile->getPathname(), $CacheFile->isFile() && $CacheFile->isReadable());
        return $CacheFile->isFile() && $CacheFile->isReadable() && $CacheFile->getSize() > 0;
    }

    /**
     * @param $name
     * @return \SplFileInfo
     */
    public function generateCacheFileByName($name) 
    {
        return new \SplFileInfo($this->cache_directory.pathinfo($name, PATHINFO_BASENAME).'.cache.php');
    }

    /**
     * @inheritdoc
     */
    public function load()
    {
        /**
         * @var \SplFileInfo $CacheFile
         */
        $list = [];
        $IniFiles = new \GlobIterator($this->getCacheDirectory().'*.cache.php');
        foreach ($IniFiles as $config_filename => $CacheFile) {
            $name = $CacheFile->getBasename('.cache.php');
            $list[$name] = $this->loadFromCache($CacheFile);
        }

        return $list;
    }
}