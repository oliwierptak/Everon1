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
use Everon\Interfaces;

class Loader implements Interfaces\ConfigLoader
{
    use Dependency\Injection\Environment;
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
    
    public function getCacheDirectory()
    {
        return $this->cache_directory;
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

        //generate router data
        if ($use_cache) {
            $CacheFile = new \SplFileInfo($this->getCacheDirectory().$ConfigFile->getBasename().'.php');
            $config_cached = $use_cache && $CacheFile->isFile();
            if ($config_cached) {
                $ini_config_data = $this->loadFromCache($CacheFile);
                $list['router'] = $this->getFactory()->buildConfigLoaderItem('router.ini', $ini_config_data);
            }
        }
        else {
            //gather router data from modules xxx
            $ini_config_data = [];
            $module_list = $this->getFileSystem()->listPathDir('//Module');
            /**
             * @var \DirectoryIterator $Dir
             */
            foreach ($module_list as $Dir) {
                $module_name = $Dir->getBasename();
                $config_filename = $this->getFileSystem()->getRealPath('//Module/'.$module_name.'/Config/router.ini');
                $module_config_data = $this->arrayPrefixKey($module_name.'@', parse_ini_file($config_filename, true));

                foreach ($module_config_data as $section => $data) {
                    $module_config_data[$section][Item\Router::PROPERTY_MODULE] = $module_name;
                }
                $ini_config_data = $this->arrayMergeDefault($ini_config_data, $module_config_data);
            }

            $list['router'] = $this->getFactory()->buildConfigLoaderItem('router.ini', $ini_config_data);
        }
        
        return $list;
    }

    /**
     * @param \SplFileInfo $ConfigFile
     * @param $use_cache
     * @return Loader\Item
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

        return $this->getFactory()->buildConfigLoaderItem($ConfigFile->getPathname(), $ini_config_data);
    }
    
    protected function loadFromCache(\SplFileInfo $CacheFile)
    {
        $filename = $CacheFile->getPathname();
        $cache = null;
        include($filename);
        return $cache;  
    }
    
    /**
     * @param Interfaces\Config $Config
     * @throws Exception\Config
     */
    public function saveConfigToCache(Interfaces\Config $Config)
    {
        try {
            $cache_filename = $this->cache_directory.pathinfo($Config->getFilename(), PATHINFO_BASENAME).'.php';
            $data = var_export($Config->toArray(), true);
            $h = fopen($cache_filename, 'w+');
            fwrite($h, "<?php \$cache = $data; ");
            fclose($h);
        }
        catch (\Exception $e) {
            throw new Exception\Config('Unable to save config cache file: "%s"', $Config->getFilename(), $e);
        }
    }
}