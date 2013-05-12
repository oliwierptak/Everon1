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

interface Logger
{
    function setLogDirectory($directory);
    function getLogDirectory();
    function setLogFiles(array $files);
    function getLogFiles();
    function warn($message, $parameters=[]);
    function error($message, $parameters=[]);
    function debug($message, $parameters=[]);
    function trace($message, $parameters=[]);
}
