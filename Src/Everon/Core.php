<?php
namespace Everon;

class Core implements Interfaces\Core
{
    use Dependency\Injection\Logger;
    

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

        if (!method_exists($Controller, $action)) {
            throw new Exception\InvalidControllerMethod(
                'Controller: "%s" has no action: "%s" defined',
                [$Controller->getName(), $action]
            );
        }

        $result = (bool) $this->executeControllerAction($Controller, $action);

        if ((bool) $this->runAfter($Controller) !== true) {
            return false;
        }

        $Controller->getResponse()->setData($Controller->getOutput());

        return $result;
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
        }

        $action = $result ? $action : $action.'OnError';
        if (method_exists($Controller->getView(), $action)) {
            $view_result = $this->executeViewAction($Controller->getView(), $action, $result);
            $result = ($view_result !== false);
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
            $View->setActionTemplate($action);
            return $View->{$action}($result);
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
    
