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

/**
 * Interface Logger
 * 
 * @method null critical
 * @method null notFound
 * 
 * @package Everon\Interfaces
 */
interface Logger
{
    function setLogDirectory($directory);
    function getLogDirectory();
    function setLogFiles(array $files);
    function getLogFiles();
    function warn($message, array $parameters=[]);
    function error($message, array $parameters=[]);
    function debug($message, array $parameters=[]);
    function trace(Exception $Message, array $parameters=[]);
    function setRequestIdentifier($request_identifier);
    function getRequestIdentifier();
}
