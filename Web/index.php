<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Everon
{
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', true);
    ini_set('xdebug.overload_var_dump', false);
    ini_set('html_errors', false);

    echo ('Start: '.memory_get_peak_usage(TRUE))."<hr/>";

    try {
        require_once(dirname(__FILE__) . '/../Src/Everon/Lib/Bootstrap.php');

        $Container = new Dependency\Container();
        $Factory = new Factory($Container);
        $Environment = $Factory->buildEnvironment(PROJECT_ROOT);

        $Container->register('Environment', function() use ($Environment) {
            return $Environment;
        });

        $log_directory = $Environment->getLog();
        $Container->register('Logger', function() use ($Factory, $log_directory) {
            return $Factory->buildLogger($log_directory);
        });

        $Container->register('Request', function() use ($Factory) {
            return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
        });

        $Container->register('Response', function() use ($Factory) {
            return $Factory->buildResponse();
        });

        $Container->register('ConfigExpressionMatcher', function() use ($Factory) {
            return $Factory->buildConfigExpressionMatcher();
        });

        $Matcher = $Container->resolve('ConfigExpressionMatcher');
        $config_directory = $Environment->getConfig();
        $config_cache_directory = $Environment->getCacheConfig();        
        $Container->register('ConfigManager', function() use ($Factory, $Matcher, $config_directory, $config_cache_directory) {
            return $Factory->buildConfigManager($Matcher, $config_directory, $config_cache_directory);
        });

        $Request = $Container->resolve('Request');
        $RouteConfig = $Container->resolve('ConfigManager')->getRouterConfig();
        $RouterValidator = $Factory->buildRouterValidator();
        $Container->register('Router', function() use ($Factory, $Request, $RouteConfig, $RouterValidator) {
            return $Factory->buildRouter($Request, $RouteConfig, $RouterValidator);
        });

        $Container->register('Core', function() use ($Factory) {
            return $Factory->buildCore();
        });

        $manager = $Container->resolve('ConfigManager')->getApplicationConfig()->go('model')->get('manager', 'Everon');
        $Container->register('ModelManager', function() use ($Factory, $manager) {
            return $Factory->buildModelManager($manager);
        });

        /**
         * @var Interfaces\Core $Application
         */
        $Application = $Container->resolve('Core');
        register_shutdown_function(array($Application, 'shutdown'));

        $Router = $Container->resolve('Router');
        $ModelManager = $Container->resolve('ModelManager');
        $compilers = $Container->resolve('ConfigManager')->getApplicationConfig()->go('template')->get('compilers');
        $directory_view_template = $Environment->getViewTemplate();
        $Igniter = function() use ($Factory, $Router, $ModelManager, $directory_view_template, $compilers) {
            $class_name = $Router->getCurrentRoute()->getController();
            $View = $Factory->buildView(
                $class_name,
                $compilers,
                $directory_view_template
            );

            return $Factory->buildController($class_name, $View, $ModelManager);
        };
        
        $Application->start($Igniter, $Container->resolve('Response'));
    }
    catch (\Exception $e)
    {
        echo '<pre><h1>500 Uncaught exception</h1>';
        echo $e."\n";
        echo str_repeat('-', strlen($e))."\n";
        if (method_exists($e, 'getTraceAsString')) {
            echo $e->getTraceAsString();
        }        
        echo '</pre>';
    }

    echo '<hr><pre>Executed in ', round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3), 's</pre>'.(memory_get_usage(TRUE))." / ".(memory_get_peak_usage(TRUE));
}


