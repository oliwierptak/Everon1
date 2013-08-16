<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Core;

use Everon\Interfaces;
use Everon\Exception;

class Mvc extends \Everon\Core implements Interfaces\Core
{
    
    protected function runMe()
    {
        /**
         * @var \Everon\Interfaces\MvcController|\Everon\Interfaces\Controller $Controller
         * @var \Everon\Interfaces\ConfigItemRouter $CurrentRoute
         */
        $CurrentRoute = $this->getRouter()->getCurrentRoute();
        $controller_name = $CurrentRoute->getController();
        $action = $CurrentRoute->getAction();

        $Controller = $this->getFactory()->buildController($controller_name);
        $this->getLogger()->actions('Executing: %s', $action);
        
        $Controller->execute($action);

        $Controller->getView()->setOutputFromAction($action, $Controller->getView()->getData());
        
        $result = $Controller->getView()->getOutput();
        $Controller->result($result, $this->getResponse());
    }
    

    /**
     * @param Interfaces\Controller $Controller
     * @return bool
     * @throws Exception\InvalidControllerMethod
     */
    public function execute(Interfaces\Controller $Controller)
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
        $controller_has_action = method_exists($Controller, $action);
        
        if ($controller_has_action) {
            $result = $Controller->{$action}();
            $result = $result === null ? true : $result; //default is true, no need to write everywhere in Controllers return true

            if ($result) {
                $result_view = $this->executeViewAction($Controller->getView(), $action);
                if ($result_view !== null) {
                    $result = $result && $result_view;
                }
            }
        }
        else {
            $result_view = $this->executeViewAction($Controller->getView(), $action);
            if ($result_view !== null) {
                $result = $result && $result_view;
            }
        }
        
        if ($result === false) {
            $result_on_error = $this->executeControllerActionOnError($Controller, $action);
            if ($result_on_error !== null) {
                $result = $result_on_error;
            }            
        }

        return $result;
    }

    /**
     * @param Interfaces\Controller $Controller
     * @param $action
     * @return bool
     */
    protected function executeControllerActionOnError(Interfaces\Controller $Controller, $action)
    {
        $action_on_error = $action.'OnError';
        if (method_exists($Controller, $action_on_error)) {
            $Controller->{$action_on_error}();
        }
        
        return $this->executeViewAction($Controller->getView(), $action_on_error);
    }

    /**
     * @param Interfaces\View $View
     * @param $action
     * @return bool|null Returns null if action was not found
     */
    protected function executeViewAction(Interfaces\View $View, $action)
    {
        if (method_exists($View, $action)) {
            $result = $View->{$action}();
            $View->setOutputFromAction($action, $View->getData());
            $result = $result === null ? true : $result;
            return $result;
        }
        
        return null;
    }

    public function shutdown()
    {
        //$this->getLogger()->trace('shutting down');
    }
    
}
