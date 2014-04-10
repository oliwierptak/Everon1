<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Mvc;

use Everon\Interfaces\TemplateContainer;
use Everon\Interfaces\View;
use Everon\Dependency;
use Everon\Domain;
use Everon\Exception;
use Everon\Helper;
use Everon\Http;
use Everon\Module;

/**
 * @method Http\Interfaces\Response getResponse()
 * @method Module\Interfaces\Mvc getModule()
 */
abstract class Controller extends \Everon\Controller
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\ViewManager;
    use Domain\Dependency\Injection\DomainManager;
    use Http\Dependency\Injection\HttpSession;

    use Helper\Arrays;
    

    /**
     * @param $action
     * @param $result
     */
    protected function prepareResponse($action, $result)
    {
        if ($result) {
            $this->executeView($action);
        }
        
        $ActionTemplate = $this->getView()->getTemplate($action, $this->getView()->getData());
        if ($ActionTemplate === null) { //apparently no template was used, fall back to string
            $ActionTemplate = $this->getView()->getContainer();
        }

        $Theme = $this->getViewManager()->getCurrentTheme();
        $Theme->set('body', $ActionTemplate);
        $data = $this->arrayMergeDefault($Theme->getData(), $ActionTemplate->getData());
        $Theme->setData($data);
        $this->getView()->setContainer($Theme->getContainer());
        $this->getViewManager()->compileView($action, $this->getView());

        $content = (string) $this->getView()->getContainer();
        $this->getResponse()->setData($content);
    }

    protected function response()
    {
        echo $this->getResponse()->toHtml();
    }
    
    /**
     * @param $action
     * @return bool
     */
    protected function executeOnError($action)
    {
        $result = parent::executeOnError($action);
        $result_view = $this->executeView($action.'OnError');
        
        if ($result === false && $result_view === false) {
            return false;
        }

        if ($result === true || $result_view === true) {
            return true;
        }
        
        return null;
    }


    protected function executeView($action)
    {
        if ($this->isCallable($this->getView(), $action)) {
            $result = $this->getView()->{$action}();
            $result = ($result !== false) ? true : $result;
            return $result;
        }
        
        return null;
    }
    
    /**
     * @return View
     */
    public function getView()
    {
        return $this->getModule()->getView($this->getName());
    }
    
    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->getDomainManager()->getModel($this->getName());
    }

    /**
     * @return TemplateContainer
     */
    public function getActionTemplate()
    {
        return $this->getView()->getTemplate($this->action, $this->getView()->getData());
    }

    /**
     * @inheritdoc
     */
    public function showException(\Exception $Exception, $code=400)
    {
        $Theme = $this->getViewManager()->getCurrentTheme();
        $Theme->set('error', $Exception);
        $data = $this->arrayMergeDefault($Theme->getData(), $this->getView()->getData());
        $Theme->setData($data);
        $this->getView()->setContainer($Theme->getContainer());
        $this->getViewManager()->compileView(null, $this->getView());
        $this->getResponse()->setData((string) $this->getView()->getContainer());

        $message = '';
        if ($Exception instanceof Http\Exception) {
            $message = $Exception->getHttpMessage();
            $code = $Exception->getHttpStatus();
        }

        $this->getResponse()->setStatusCode($code);
        $this->getResponse()->setStatusMessage($message);
        $this->response();
    }
    
    public function redirect($name)
    {
        $route = $this->getConfigManager()->getConfigValue('router.'.$name);
        if ($route === null) {
            throw new Exception\Controller('Invalid router config name: "%s"', $name);
        }
        $this->getResponse()->setHeader('refresh', '1; url='.$route['url']);
    }
}