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

interface FileSystem
{
    function getRoot();

    /**
     * @param $path
     * @param int $mode
     * @throws Exception\FileSystem
     */    
    function createPath($path);

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

    function createTmpFile();
    
    function writeTmpFile($handler, $content);
    
    function getTmpFilename($handler);

    function closeTmpFile($handler);
}