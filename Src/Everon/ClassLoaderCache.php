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

class ClassLoaderCache extends ClassLoader implements Interfaces\ClassLoader
{
    /**
     * @var Interfaces\ClassMap
     */
    protected $ClassMap = null;


    /**
     * @param Interfaces\ClassMap $ClassMap
     */
    public function __construct(Interfaces\ClassMap $ClassMap)
    {
        $this->ClassMap = $ClassMap;
    }

    /**
     * @param $class_name
     * @return null|string|void
     */
    public function load($class_name)
    {
        $filename = $this->ClassMap->getFilenameFromMap($class_name);
        if ($filename !== null) {
            include_once($filename);
            return $filename;
        }

        $filename = parent::load($class_name);
        $this->ClassMap->addToMap($class_name, $filename);
        
        return $filename;
    }

}