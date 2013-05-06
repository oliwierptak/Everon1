<?php
namespace Everon;

class Core implements Interfaces\Core
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\Logger;
    use Dependency\Injection\Response;
    use Dependency\Injection\Router;
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\ModelManager;
    
    public function start()
    {
        try {
            $class_name = $this->getRouter()->getCurrentRoute()->getController();
            /**
             * @var \Everon\Config $ApplicationConfig
             */
            $View = $this->getFactory()->buildView(
                $class_name,
                $this->getConfigManager()->getApplicationConfig()->go('template')->get('compilers')
            );
    
            $Controller = $this->getFactory()->buildController($class_name, $View, $this->getModelManager());
            
            $result = $this->run($Controller);
            $this->getResponse()->setData($Controller->getOutput());
            $this->getResponse()->setResult($result);
            
            $this->result($Controller->getRouter()->getCurrentRoute()->getName());
        }
        catch (Exception\InvalidRouterParameter $e)
        {
            //todo: raise event for from validation
            throw $e;
        }
        catch (Exception\PageNotFound $e)
        {
            $this->getLogger()->error($e);
            echo '<pre><h1>404 Page not found</h1>';
            echo '<code>'.$e.'</code>';
        }
        catch (Exception\DomainException $e) {
            $this->getLogger()->error($e);
            echo '<pre><h1>Domain Exception</h1>';
            echo '<code>'.$e.'</code>';
        }
        catch (Exception $e)
        {
            $this->getLogger()->error($e);
            echo '<pre><h1>500 Everon Error</h1>';
            echo $e."\n";
            echo str_repeat('-', strlen($e))."\n";
            if (method_exists($e, 'getTraceAsString')) {
                echo $e->getTraceAsString();
            }
            echo '</pre>';
        }        
    }

    /**
     * @param Interfaces\Controller $Controller
     * @return bool
     * @throws Exception\InvalidControllerMethod
     */
    public function run(Interfaces\Controller $Controller)
    {
        $action = $this->getControllerActionNameFromRoute($Controller);
        if ((bool) $this->runBefore($Controller) !== true) {
            return false;
        }

        if (method_exists($Controller, $action) === false && method_exists($Controller->getView(), $action) === false ) {
            throw new Exception\InvalidControllerMethod(
                'Controller: "%s" has no action: "%s" defined',
                [$Controller->getName(), $action]
            );
        }

        $result = (bool) $this->executeControllerAction($Controller, $action);

        if ((bool) $this->runAfter($Controller) !== true) {
            return false;
        }
        
        return $result;
    }
    
    public function result($route_name)
    {
        if ($this->getResponse()->getResult() === false) {
            $data = vsprintf('Invalid response for route: "%s"', [$route_name]);
            $this->getResponse()->setData($data);
        }

        $this->getResponse()->send();
        echo $this->getResponse()->toHtml();        
    }

    /**
     * @param Interfaces\Controller $Controller
     * @return bool
     */
    protected function runBefore(Interfaces\Controller $Controller)
    {
        $action_before = 'before'.ucfirst($this->getControllerActionNameFromRoute($Controller));
        return $this->executeControllerAction($Controller, $action_before);
    }

    /**
     * @param Interfaces\Controller $Controller
     * @return bool
     */
    protected function runAfter(Interfaces\Controller $Controller)
    {
        $action_after = 'after'.ucfirst($this->getControllerActionNameFromRoute($Controller));
        return $this->executeControllerAction($Controller, $action_after);
    }

    /**
     * @param Interfaces\Controller $Controller
     * @param $action
     * @return bool
     */
    protected function executeControllerAction(Interfaces\Controller $Controller, $action)
    {
        $result = true;
        if (method_exists($Controller, $action)) {
            $result = $Controller->{$action}();
            $result = $result === null ? true : $result; //default is true, no need to write everywhere return true
        }
        
        $action = $result ? $action : $action.'OnError';
        if (method_exists($Controller->getView(), $action)) {
            $view_result = $this->executeViewAction($Controller->getView(), $action, $result);
            $view_result = $view_result === null ? true : $view_result; //default is true, no need to write everywhere return true
            $result = $result && $view_result;
        }
        
        /*$models = $Controller->getAllModels();
        if (is_array($models)) {
            $this->executeModelAction($models, $action);
        }*/
        return $result;
    }

    /**
     * @param Interfaces\View $View
     * @param $action
     * @param $result
     */
    protected function executeViewAction(Interfaces\View $View, $action, $result)
    {
        if (method_exists($View, $action)) {
            $result = $View->{$action}($result);
            $View->setTemplateFromAction($action, $View->getData());
            return $result;
        }
        
        return $result;
    }

    /**
     * @param array $models
     * @param $action
     */
    protected function executeModelAction(array $models, $action)
    {
        foreach ($models as $name => $Model) {
            if (method_exists($Model, $action)) {
                $Model->{$action}();
            }
        }
    }

    /**
     * @param Interfaces\Controller $Controller
     * @return string
     */
    protected function getControllerActionNameFromRoute(Interfaces\Controller $Controller)
    {
        return $Controller->getRouter()->getCurrentRoute()->getAction();
    }

    public function shutdown()
    {
        //$this->getLogger()->trace('shutting down');
    }
    
}
