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
     * @inheritdoc
     */
    public function load()
    {
        /**
         * @var \SplFileInfo $CacheFile
         */
        $type = strtolower($this->getType());
        $list = [];
        $IniFiles = new \GlobIterator($this->getCacheDirectory().'*.'.$type.'.php');
        foreach ($IniFiles as $config_filename => $CacheFile) {
            $name = $CacheFile->getBasename('.'.$type.'.php');
            $list[$name] = $this->loadFromCache($CacheFile);
        }

        return $list;
    }
}