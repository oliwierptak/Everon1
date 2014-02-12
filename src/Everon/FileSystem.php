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

class FileSystem implements Interfaces\FileSystem
{
    /**
     * @var string location of the file system root folder
     */
    protected $root = null;

    /**
     * @param $root
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    /**
     * @param $path
     * @return string
     */
    protected function getRelativePath($path)
    {
        $is_absolute = mb_strtolower($this->root) === mb_strtolower(mb_substr($path, 0, mb_strlen($this->root)));
        if ($path[0] === '/' && $path[1] === '/') { //eg. '//Tests/Everon/tmp/'
            //strip virtual root
            $path = mb_substr($path, 2, mb_strlen($path));
        }
        else if ($is_absolute) { //absolute, eg. '/var/www/Everon/Tests/Everon/tmp/';
            //strip absolute root from path
            $path = mb_substr($path, mb_strlen($this->root));
        }        
        
        $path = $this->root.$path;
        return $path;
    }
    
    public function getRealPath($path)
    {
        return $this->getRelativePath($path);
    }

    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @inheritdoc
     */
    public function createPath($path, $mode=0775)
    {
        try {
            $path = $this->getRelativePath($path);
            if (is_dir($path) === false) {
                mkdir($path, $mode, true);
            }
        }
        catch (\Exception $e) {
            throw new Exception\FileSystem($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function deletePath($path)
    {
        try {
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
        catch (\Exception $e) {
            throw new Exception\FileSystem($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function listPath($path)
    {
        try {
            $result = [];
            $path = $this->getRelativePath($path);
            $files = new \GlobIterator($path.DIRECTORY_SEPARATOR.'*.*');
            
            foreach ($files as $File) {
                $result[] = $File;
            }
            
            return $result;
        }
        catch (\Exception $e) {
            throw new Exception\FileSystem($e);
        }            
    }
    
    /**
     * @inheritdoc
     */
    public function listPathDir($path)
    {
        try {
            $result = [];
            $path = $this->getRelativePath($path);
            $directories = new \DirectoryIterator($path.DIRECTORY_SEPARATOR);

            /**
             * @var \DirectoryIterator $Dir
             */
            foreach ($directories as $Dir) {
                if ($Dir->isDot() === false) {
                    $result[] = clone $Dir;
                }
            }
            
            return $result;
        }
        catch (\Exception $e) {
            throw new Exception\FileSystem($e);
        }            
    }

    /**
     * @inheritdoc
     */
    function create($filename)
    {
        $filename = $this->getRelativePath($filename);
        return new \SplFileInfo($filename);
    }

    /**
     * @inheritdoc
     */
    public function save($filename, $content)
    {
        try {
            $filename = $this->getRelativePath($filename);
            $Filename = new \SplFileInfo($filename);
            $this->createPath($Filename->getPath());
            $h = fopen($Filename->getPathname(), 'w');
            fwrite($h, $content);
            fclose($h);
        }
        catch (\Exception $e) {
            throw new Exception\FileSystem($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function load($filename)
    {
        try {
            $filename = $this->getRelativePath($filename);
            if (file_exists($filename) === false) {
                return null;
            }
            
            return file_get_contents($filename);
        }
        catch (\Exception $e) {
            throw new Exception\FileSystem($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete($filename)
    {
        try {
            $filename = $this->getRelativePath($filename);
            unlink($filename);
        }
        catch (\Exception $e) {
            throw new Exception\FileSystem($e);
        }
    }

    /**
     * @return FileSystem\TmpFile
     */
    public function createTmpFile()
    {
        return new FileSystem\TmpFile();
    }
}