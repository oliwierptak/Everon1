<?php
/*
 * Everon
 * (c) 2011-2013 Oliwier Ptak
 * */

namespace Everon
{
    echo ('Start: '.memory_get_peak_usage(TRUE))."<hr/>";

    try {
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', true);
        ini_set('xdebug.overload_var_dump', false);
        ini_set('html_errors', false);
        
        require_once(dirname(__FILE__) . '/../Src/Everon/Lib/Bootstrap.php');

        $Container = new Dependency\Container();
        $Factory = new Factory($Container);

        $Container->register('Logger', function() use ($Factory) {
            return $Factory->buildLogger(ev_DIR_LOG);
        });

        $Container->register('Request', function() use ($Factory) {
            return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
        });

        $Container->register('Response', function() use ($Factory) {
            return $Factory->buildResponse();
        });

        $Container->register('ConfigManager', function() use ($Factory, $Container) {
            return $Factory->buildConfigManager(
                $Container->resolve('ConfigExpressionMatcher'),  
                ev_DIR_CONFIG, 
                ev_DIR_CACHE_CONFIG
            );
        });

        $Container->register('ConfigExpressionMatcher', function() use ($Factory, $Container) {
            return $Factory->buildConfigExpressionMatcher();
        });

        $Container->register('Router', function() use ($Factory, $Container) {
            return $Factory->buildRouter(
                $Container->resolve('Request'), 
                $Container->resolve('ConfigManager')->getRouterConfig()
            );
        });

        $Container->register('Core', function() use ($Factory) {
            return $Factory->buildCore();
        });

        $Container->register('ModelManager', function() use ($Factory, $Container) {
            /**
             * @var \Everon\Config $Config
             */
            $Config = $Container->resolve('ConfigManager')->getApplicationConfig();
            return $Factory->buildModelManager($Config->go('model')->get('manager'));
        });
        
        /**
         * @var \Everon\Interfaces\ModelManager $ModelManager
         */
        //$ModelManager = $DependencyContainer->resolve('ModelManager');
        //$ModelManager->init();

        $class_name = $Container->resolve('Router')->getCurrentRoute()->getController();
        /**
         * @var \Everon\Config $ApplicationConfig
         */
        $ApplicationConfig = $Container->resolve('ConfigManager')->getApplicationConfig();
        $View = $Factory->buildView(
            $class_name,
            $ApplicationConfig->go('template')->get('compilers')
        );
        
        $Controller = $Factory->buildController($class_name, $View);

        /**
         * @var Core $Application
         */
        $Application = $Container->resolve('Core');
        $result = $Application->run($Controller);

        if ($result === true) {
            /**
             * @var \Everon\Response $Response
             */
            $Response = $Controller->getResponse();
            $Response->send();
            echo $Response->toHtml();
        }
        else {
            throw new \Everon\Exception\InvalidControllerResponse('Invalid controller response for route: "%s"', [
                $Controller->getRouter()->getCurrentRoute()->getName()
            ]);
        }

        register_shutdown_function(array($Application, 'shutdown'));
    }
    catch (Exception\InvalidControllerResponse $e)
    {
        echo '<pre><h1>500 Error has occurred</h1>';
        echo '<code>'.$e.'</code>';
    }
    catch (Exception\PageNotFound $e)
    {
        echo '<pre><h1>Page not found</h1>';
        echo '<code>'.$e.'</code>';
    }
    catch (\Exception $e)
    {
        echo '<pre><h1>Everon Error</h1>';
        echo $e."\n";
        echo str_repeat('-', strlen($e))."\n";
        if (method_exists($e, 'getTraceAsString')) {
            echo $e->getTraceAsString();
        }
        echo '</pre>';
    }

    echo '<hr><pre>Executed in ', round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3), 's</pre>'.(memory_get_usage(TRUE))." / ".(memory_get_peak_usage(TRUE));
}


