<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

use Everon\Exception;

interface FileSystem extends Dependency\Factory
{
    /**
     * @param $path
     * @return bool
     */
    function directoryExists($path);

    /**
     * @param $path
     * @return bool
     */
    function fileExists($path);
        
    function getRealPath($path);
    
    function getRoot();

    /**
     * @param $root
     * @throws Exception\FileSystem
     */
    function setRoot($root);

    /**
     * @param $path
     * @param int $mode
     * @throws Exception\FileSystem
     */
    function createPath($path, $mode=0775);

    /**
     * @param $path
     * @throws Exception\FileSystem
     */    
    function deletePath($path);

    /**
     * @param $path
     * @return array
     */    
    function listPath($path);
    
    /**
     * @param $path
     * @return array
     */    
    function listPathDir($path);

    /**
     * @param $filename
     * @return \SplFileInfo
     */    
    function create($filename);
    
    /**
     * @param $filename
     * @param $content
     * @throws Exception\FileSystem
     */    
    function save($filename, $content);

    /**
     * @param $filename
     * @return string
     * @throws Exception\FileSystem
     */    
    function load($filename);

    /**
     * @param $filename
     * @throws Exception\FileSystem
     */    
    function delete($filename);

    /**
     * @return \Everon\Interfaces\FileSystemTmpFile
     */
    function createTmpFile();

    /**
     * @param $file_path
     * @param $destination
     * @internal param bool $create_directory
     * @return bool
     */
    function moveUploadedFile($file_path, $destination);

    /**
     * @param $source
     * @param $target
     */
    function renameFile($source, $target);

    /**
     * @param $sourcePath
     * @param $targetPath
     * @return bool
     */
    function copyFile($sourcePath, $targetPath);
}