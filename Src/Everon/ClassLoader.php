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

class ClassLoader implements Interfaces\ClassLoader
{
    protected $resources = [];

    public function register()
    {
        spl_autoload_register([$this, 'load']);
    }
    
    public function unRegister()
    {
        spl_autoload_unregister([$this, 'load']);
    }

    /**
     * @param $namespace
     * @param $directory
     */
    public function add($namespace, $directory)
    {
        $this->resources[$namespace] = $directory;
    }

    /**
     * @param $class_name
     * @return string
     * @throws \RuntimeException
     */
    public function load($class_name)
    {
        $included = false;
        $filename = '';
        foreach ($this->resources as $namespace => $path) {
            $filename = $path.str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
            $included = $this->includeWhenExists($filename);
            if ($included) {
                break;
            }

            $filename = $path.trim(str_replace($namespace, '', $class_name), '\\').'.php';
            $included = $this->includeWhenExists($filename);
            if ($included) {
                break;
            }
        }

        if ($included === false) {
            throw new \RuntimeException(vsprintf(
                'Class: "%s" for file: "%s" could not be found', [$class_name, $filename]
            ));
        }
        
        return $filename;
    }

    /**
     * @param $filename
     * @return bool
     */
    protected function includeWhenExists($filename)
    {
        $exists = file_exists($filename);

        if ($exists) {
            include_once($filename);
            return true;
        }

        return false;
    }

}