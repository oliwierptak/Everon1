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

    /**
     * @param $config_directory
     */
    public function __construct($config_directory)
    {
        $this->config_directory = $config_directory;
    }

    /**
     * @inheritdoc
     */
    public function getConfigDirectory()
    {
        return $this->config_directory;
    }

    /**
     * @inheritdoc
     */
    public function setConfigDirectory($config_directory)
    {
        $this->config_directory = $config_directory;
    }
    
    /**
     * @inheritdoc
     */
    public function readIni($filename)
    {
        return @parse_ini_file($filename, true);
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
        $IniFiles = new \GlobIterator($this->getConfigDirectory().'*.ini');
        foreach ($IniFiles as $config_filename => $ConfigFile) {
            $name = $ConfigFile->getBasename('.ini');
            $list[$name] = $this->loadFromFile($ConfigFile);
        }

        return $list;
    }
    
    /**
     * @inheritdoc
     */
    public function loadFromFile(\SplFileInfo $ConfigFile)
    {
        $ini_config_data =  $this->readIni($ConfigFile->getPathname());
        
        if (is_array($ini_config_data) === false) {
            throw new Exception\Config('Config data not found for: "%s"', $ConfigFile->getBasename());
        }

        return $this->getFactory()->buildConfigLoaderItem($ConfigFile->getPathname(), $ini_config_data);
    }

}