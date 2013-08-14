<?php
/**
 * Everon application example.
 */

namespace Everon
{
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', true);
    ini_set('xdebug.overload_var_dump', false);
    ini_set('html_errors', false);

    echo ('Start: '.memory_get_usage(TRUE)/1024)." kb<hr/>";

    try {
        require_once(dirname(__FILE__) . '/../Src/Everon/Lib/Bootstrap.php');

        $Container = new Dependency\Container();
        $Factory = new Factory($Container);

        $Container->register('Environment', function() use ($Factory) {
            return $Factory->buildEnvironment(PROJECT_ROOT);
        });
        
        $Container->register('Logger', function() use ($Factory) {
            $log_directory = $Factory->getDependencyContainer()->resolve('Environment')->getLog();
            return $Factory->buildLogger($log_directory);
        });
        
        $Container->register('CurlyCompiler', function() use ($Factory) {
            return $Factory->buildTemplateCompiler('Curly');
        });

        $Container->register('Request', function() use ($Factory) {
            return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
        });

        $Container->register('Response', function() use ($Factory) {
            $Headers = $Factory->buildHttpHeaderCollection();
            return $Factory->buildResponse($Headers);
        });

        $Container->register('ConfigManager', function() use ($Factory) {
            /**
             * @var \Everon\Interfaces\Environment $Environment
             */
            $Environment = $Factory->getDependencyContainer()->resolve('Environment');
            $config_cache_directory = $Environment->getCacheConfig();
            $Matcher = $Factory->buildConfigExpressionMatcher();
            $Loader = $Factory->buildConfigLoader($Environment->getConfig(), $config_cache_directory);
            return $Factory->buildConfigManager($Loader, $Matcher);
        });

        $Container->register('Router', function() use ($Factory) {
            $Request = $Factory->getDependencyContainer()->resolve('Request');
            $RouteConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getRouterConfig();            
            $RouterValidator = $Factory->buildRouterValidator();
            return $Factory->buildRouter($Request, $RouteConfig, $RouterValidator);
        });

        $Container->register('ModelManager', function() use ($Factory) {
            $Config = $Factory->getDependencyContainer()->resolve('ConfigManager')->getApplicationConfig();
            return $Factory->buildModelManager(
                $Config->go('model')->get('manager', 'Everon')
            );
        });

        /**
         * @var Interfaces\Core $Application
         */
        $Application = $Factory->buildCore();
        register_shutdown_function(array($Application, 'shutdown'));
        
        $Igniter = function() use ($Factory) {
            $Container = $Factory->getDependencyContainer();
            $ApplicationConfig = $Container->resolve('ConfigManager')->getApplicationConfig();
            $class_name = $Container->resolve('Router')->getCurrentRoute()->getController();
            
            $ViewManager = $Factory->buildViewManager(
                $ApplicationConfig->go('view')->get('manager', 'Everon'),
                $ApplicationConfig->go('view')->get('compilers', []),
                $Container->resolve('Environment')->getViewTemplate()
            );

            return $Factory->buildController($class_name, $ViewManager, $Container->resolve('ModelManager'));
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

    echo '<hr><pre>Executed in ', round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3), 's</pre>'.(memory_get_usage(TRUE)/1024)." / ".(memory_get_peak_usage(TRUE)/1024).' kb';
}


