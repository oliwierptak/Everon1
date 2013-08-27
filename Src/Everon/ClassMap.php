<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

use Everon\Interfaces;

class ClassMap implements Interfaces\ClassMap
{
    /**
     * @var array
     */
    protected $class_map = null;

    /**
     * @var \SplFileInfo
     */
    protected $class_map_filename = null;


    /**
     * @param $filename
     */
    public function __construct($filename)
    {
        $this->class_map_filename = new \SplFileInfo($filename);
    }

    /**
     * @param $class
     * @param $file
     */
    public function addToMap($class, $file)
    {
        $this->loadMap();
        if (isset($this->class_map[$class]) === false) {
            $this->class_map[$class] = $file;
            $this->saveMap();
        }
    }

    /**
     * @param $class
     * @return null
     */
    public function getFilenameFromMap($class)
    {
        $this->loadMap();
        if (isset($this->class_map[$class])) {
            return $this->class_map[$class];
        }

        return null;
    }

    /**
     * @return \SplFileInfo
     */
    protected function getCacheFilename()
    {
        return $this->class_map_filename;
    }

    public function loadMap()
    {
        if ($this->class_map !== null) {
            return;
        }
        
        $Filename = $this->getCacheFilename();
        if ($Filename->isFile()) {
            $this->class_map = include($Filename);
        }
    }

    protected function saveMap()
    {
        try {
            $this->loadMap();
            $data = var_export($this->class_map, 1);
            $Filename = $this->getCacheFilename();
            if ($Filename->isWritable()) {
                $h = fopen($Filename, 'w+');
                fwrite($h, "<?php return $data; ");
                fclose($h);                
            }
        }
        catch (\Exception $e) {
            return null;
        }
    }

}