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
use Everon\Exception;
use Everon\Helper;

class Loader implements Interfaces\Loader
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\FileSystem;
    use Helper\Arrays;
    
    protected $config_directory = null;
    protected $cache_directory = null;

    /**
     * @param $config_directory
     * @param $cache_directory
     */
    public function __construct($config_directory, $cache_directory)
    {
        $this->config_directory = $config_directory;
        $this->cache_directory = $cache_directory;
    }
    
    public function getConfigDirectory()
    {
        return $this->config_directory;
    }

    /**
     * @param $config_directory
     */
    public function setConfigDirectory($config_directory)
    {
        $this->config_directory = $config_directory;
    }
    
    public function getCacheDirectory()
    {
        return $this->cache_directory;
    }

    /**
     * @param $cache_directory
     */
    public function setCacheDirectory($cache_directory)
    {
        $this->cache_directory = $cache_directory;
    }

    /**
     * @param $filename
     * @return array|null
     */
    public function read($filename)
    {
        return @parse_ini_file($filename, true);
    }

    /**
     * @param $use_cache
     * @return array
     */
    public function load($use_cache)
    {
        /**
         * @var \SplFileInfo $ConfigFile
         * @var \Closure $Compiler
         */
        $list = [];
        $IniFiles = new \GlobIterator($this->getConfigDirectory().'*.ini');
        foreach ($IniFiles as $config_filename => $ConfigFile) {
            $name = $ConfigFile->getBasename('.ini');
            $list[$name] = $this->loadByFile($ConfigFile, $use_cache);
        }

        return $list;
    }
    
    /**
     * @param \SplFileInfo $ConfigFile
     * @param $use_cache
     * @return Interfaces\LoaderItem
     */
    public function loadByFile(\SplFileInfo $ConfigFile, $use_cache)
    {
        $CacheFile = new \SplFileInfo($this->getCacheDirectory().$ConfigFile->getBasename().'.php');
        $config_cached = $use_cache && $CacheFile->isFile();
        if ($config_cached) {
            $ini_config_data = $this->loadFromCache($CacheFile);
        }
        else {
            $config_filename = $ConfigFile->getPathname();
            $ini_config_data =  parse_ini_file($config_filename, true);
        }
        
        if (is_array($ini_config_data) === false) {
            throw new Exception\Config('Cache data not found for: "%s"', $CacheFile->getBasename());
        }

        return $this->getFactory()->buildConfigLoaderItem($ConfigFile->getPathname(), $ini_config_data, $use_cache);
    }
    
    protected function loadFromCache(\SplFileInfo $CacheFile)
    {
        $filename = $CacheFile->getPathname();
        $cache = null;
        include($filename);
        return $cache;  
    }
    
    /**
     * @param \Everon\Interfaces\Config $Config
     * @throws Exception\Config
     */
    public function saveConfigToCache(\Everon\Interfaces\Config $Config)
    {
        try {
            $cache_filename = strtolower(str_replace('@', '_', $Config->getName()));
            $CacheFile = new \SplFileInfo($this->cache_directory.pathinfo($cache_filename, PATHINFO_BASENAME).'.ini.php');
            
            if ($CacheFile->isFile() === false) {
                $cache_data = [
                    'default_item' => $Config->getDefaultItem(),
                    'items' => $Config->getItems(),
                    'filename' => $Config->getFilename(),
                    'name' => $Config->getName()
                ];
                $data = var_export($cache_data, true);
                $h = fopen($CacheFile->getPathname(), 'w+');
                fwrite($h, "<?php \$cache = $data; ");
                fclose($h);
            }
        }
        catch (\Exception $e) {
            throw new Exception\Config('Unable to save config cache file: "%s"', $Config->getFilename(), $e);
        }
    }
}