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

use Everon\Application;

interface Bootstrap
{
    /**
     * @param bool $show_auto_loader_exceptions
     */
    function setShowAutoLoaderExceptions($show_auto_loader_exceptions);

    /**
     * @return bool
     */
    function getShowAutoLoaderExceptions();

    /**
     * @return ClassLoader
     */
    function getClassLoader();

    /**
     * @return Environment
     */
    function getEnvironment();

    /**
     * @param Environment $Environment
     */
    function setEnvironment(Environment $Environment);

    /**
     * @return string
     */
    function getOsName();

    /**
     * @param $name
     * @return bool
     */
    function hasAutoloader($name);

    /**
     * @param bool $prepend_autoloader
     * @return Application\Factory
     */
    function run($prepend_autoloader = false);

    /**
     * @param $guid_value
     * @param $app_root
     * @param $log_filename
     */
    static function setupExceptionHandler($guid_value, $app_root, $log_filename);

    /**
     * @param \Exception $Exception
     * @param $guid_value
     * @param $log_filename
     * @return string
     */
    static function logException(\Exception $Exception, $guid_value, $log_filename);

    /**
     * @param string $environment_name
     */
    function setEnvironmentName($environment_name);

    /**
     * @return string
     */
    function getEnvironmentName();
}