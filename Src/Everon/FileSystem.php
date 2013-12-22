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
use Everon\Exception;

class Filesystem implements Interfaces\FileSystem
{
    protected $root = null;
    
    public function __construct($root)
    {
        $this->root = $root;
    }
    
    protected function getRelativePath($path)
    {
        if ($path[0] === DIRECTORY_SEPARATOR && $path[1] === DIRECTORY_SEPARATOR) { //eg. '//Tests/Everon/tmp/'
            //strip semi-root
            $path = substr($path, 2, strlen($path));
        }
        
        $path = $this->root.$path;
        return $path;
    }
    
    public function getRoot()
    {
        return $this->root;
    }
    
    public function createPath($path, $mode=0775)
    {
        try {
            $path = $this->getRelativePath($path);
            mkdir($path, $mode, true);
        }
        catch (\Exception $e) {
            throw new Exception\FileSystem($e);
        }
    }
    
    public function deletePath($path)
    {
        $path = $this->getRelativePath($path);
        
        if (is_dir($path)) {
            $It = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            array_map('unlink', iterator_to_array($It));
            rmdir($path);
        }        
    }
    
    public function listPath($path)
    {
        $result = [];
        $path = $this->getRelativePath($path);
        $Files = new \GlobIterator($path.'*.*');
        
        foreach ($Files as $filename => $File) {
            $result[] = $File;
        }
        
        return $result;
    }
}