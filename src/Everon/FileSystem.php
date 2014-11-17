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
    use Dependency\Injection\Factory;
    
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
     * @throws Exception\FileSystem
     */
    protected function getRelativePath($path)
    {
        $path = trim($path);
        
        if ($path === '') {
            throw new Exception\FileSystem('Invalid path');
        }
        
        if ($path === '/') {
            $path = '//';
        }
        
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

    /**
     * @inheritdoc
     */
    public function directoryExists($path)
    {
        $path = $this->getRealPath($path);
        return (new \SplFileInfo($path))->isDir();
    }

    /**
     * @inheritdoc
     */
    public function fileExists($path)
    {
        $path = $this->getRealPath($path);
        return (new \SplFileInfo($path))->isFile();
    }

    /**
     * @inheritdoc
     */
    public function getRealPath($path)
    {
        return $this->getRelativePath($path);
    }

    /**
     * @inheritdoc
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @inheritdoc
     */
    public function setRoot($root)
    {
        /**
         * @var \SplFileInfo $Root
         */
        $Root = new \SplFileInfo($root);
        if ($Root->isDir() === false) {
            throw new Exception\FileSystem('Root directory does not exist: "%s"', $root);
        }
        
        $this->root = $Root->getPathname().DIRECTORY_SEPARATOR;
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
    public function copyPath($source, $destination, $mode=0775)
    {
        $this->createPath($destination,$mode);

        /**
         * @var \RecursiveIteratorIterator $Iterator
         */
        $Iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST);

        /**
         * @var \SplFileInfo $Item
         */
        foreach ($Iterator as $Item) {
            if ($Item->isDir()) {
                $this->createPath($destination . DIRECTORY_SEPARATOR . $Iterator->getSubPathName());
            }
            else {
                copy($Item, $destination. DIRECTORY_SEPARATOR . $Iterator->getSubPathName());
            }
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
            if ((new \SplFileInfo($path))->isDir() === false) {
                return [];
            }
            $files = new \GlobIterator($path.DIRECTORY_SEPARATOR.'*.*');

            /**
             * @var \DirectoryIterator $File
             */
            foreach ($files as $File) {
                $name = $File->getBasename();
                if ($File->isFile() && $name[0] != '.') {
                    $result[] = $File;
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
    public function listPathDir($path)
    {
        try {
            $result = [];
            $path = $this->getRelativePath($path);
            if ((new \SplFileInfo($path))->isDir() === false) {
                return [];
            }
            $directories = new \DirectoryIterator($path.DIRECTORY_SEPARATOR);

            /**
             * @var \DirectoryIterator $Dir
             */
            foreach ($directories as $Dir) {
                $name = $Dir->getBasename();
                if ($Dir->isDot() === false && $Dir->isDir() && $name[0] !== '.') {
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
     * @return FileSystem\Interfaces\TmpFile
     */
    public function createTmpFile()
    {
        return $this->getFactory()->buildFileSystemTmpFile();
    }

    /**
     * @inheritdoc
     */
    public function moveUploadedFile($file_path, $destination, $create_directory = true)
    {
        if (is_uploaded_file($file_path) === false) {
            throw new Exception\FileSystem('The file needs to be uploaded in order to be moved');
        }

        try {
            $directory = pathinfo($destination, PATHINFO_DIRNAME);
            if ($this->directoryExists($directory) === false && $create_directory === true) {
                $this->createPath($directory);
            }

            return move_uploaded_file($file_path, $destination) === true;
        }
        catch (\Exception $e) {
            throw new Exception\FileSystem($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function renameFile($source, $target)
    {
        if ($this->fileExists($source) === false) {
            return false;
        }

        return rename($source, $target);
    }

    /**
     * @inheritdoc
     */
    public function copyFile($sourcePath, $targetPath)
    {
        if ($this->fileExists($sourcePath) === false) {
            return false;
        }

        return copy($sourcePath, $targetPath);
    }
}