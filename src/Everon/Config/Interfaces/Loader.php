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
     * @return array
     */
    function load();

    /**
     * @param \SplFileInfo $ConfigFile
     * @return LoaderItem
     * @throws Exception\Config
     */
    function loadFromFile(\SplFileInfo $ConfigFile);

    /**
     * @param $filename
     * @return array|null
     */
    function readIni($filename);
}
