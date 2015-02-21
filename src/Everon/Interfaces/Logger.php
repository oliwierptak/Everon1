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

interface Logger
{
    /**
     * @param $directory
     */
    function setLogDirectory($directory);
    
    function getLogDirectory();

    /**
     * @param array $files
     */
    function setLogFiles(array $files);

    /**
     * @return array
     */
    function getLogFiles();

    /**
     * @param $message
     * @param array $parameters
     * @return \DateTime
     */
    function warn($message, array $parameters=[]);

    /**
     * @param $message
     * @param array $parameters
     * @return \DateTime
     */
    function error($message, array $parameters=[]);

    /**
     * @param $message
     * @param array $parameters
     * @return \DateTime
     */
    function debug($message, array $parameters=[]);
    
    /**
     * @param $log_name
     * @param $message
     * @param array $parameters
     * @return \DateTime
     */
    function log($log_name, $message, array $parameters=[]);

    /**
     * @param $log_name
     */
    function logReset($log_name);

    /**
     * @param \Exception $Message
     * @param array $parameters
     * @return \DateTime
     */
    function trace(\Exception $Message, array $parameters=[]);

    /**
     * @param $request_identifier
     */
    function setRequestIdentifier($request_identifier);

    /**
     * @return string
     */
    function getRequestIdentifier();

    /**
     * @return \DateTimeZone
     */
    function getLogDateTimeZone();

    /**
     * @return string
     */
    function getLogDateFormat();
}
