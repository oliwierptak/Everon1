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

interface Loader extends Interfaces\Dependency\Factory, Interfaces\Dependency\FileSystem
{
    function getConfigDirectory();

    /**
     * @param $config_directory
     */
    function setConfigDirectory($config_directory);

    /**
     * @param string $config_flavour_directory
     */
    function setConfigFlavourDirectory($config_flavour_directory);

    /**
     * @return string
     */
    function getConfigFlavourDirectory();
    
    /**
     * @return array
     */
    function load();

    /**
     * @param $directory
     * @return array
     */
    function loadFromDirectory($directory);

    /**
     * @param \SplFileInfo $ConfigFile
     * @return array(filename=string, data=array)
     * @throws Exception\Config
     */
    function loadFromFile(\SplFileInfo $ConfigFile);

    /**
     * @param $filename
     * @return array|null
     */
    function readIni($filename);
}
