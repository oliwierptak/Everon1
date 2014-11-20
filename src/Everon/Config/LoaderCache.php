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

class LoaderCache implements Interfaces\LoaderCache
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\FileSystem;
    use Helper\Arrays;
    
    protected $cache_directory = null;

    /**
     * @param $cache_directory
     */
    public function __construct($cache_directory)
    {
        $this->cache_directory = $cache_directory;
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
     * @inheritdoc
     */
    public function load()
    {
        /**
         * @var \SplFileInfo $ConfigFile
         * @var \Closure $Compiler
         */
        $list = [];
        $IniFiles = new \GlobIterator($this->getCacheDirectory().'*.ini.php');
        foreach ($IniFiles as $config_filename => $ConfigFile) {
            $name = $ConfigFile->getBasename('.ini.php');
            $list[$name] = $this->loadFromCache($ConfigFile);
        }

        return $list;
    }

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
    public function saveConfigToCache(\Everon\Interfaces\Config $Config)
    {
        try {
            $CacheFile = new \SplFileInfo($this->cache_directory.pathinfo($Config->getName(), PATHINFO_BASENAME).'.ini.php');
            
            if ($CacheFile->isFile() === false && is_dir($CacheFile->getPath())) {
                $cache_data = [
                    'default_item' => $Config->getDefaultItem()->getName(),
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