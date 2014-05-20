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

use Everon\Dependency;
use Everon\Domain;
use Everon\Exception;
use Everon\Helper;
use Everon\Http;
use Everon\Module;
use Everon\View;

/**
 * @method Http\Interfaces\Response getResponse()
 * @method Module\Interfaces\Mvc getModule()
 */
abstract class Controller extends \Everon\Controller
{
    use Dependency\Injection\Factory;
    use View\Dependency\Injection\ViewManager;
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
            $this->executeView($this->getView(), $action);
        }
        
        $ActionTemplate = $this->getView()->getTemplate($action, $this->getView()->getData());
        if ($ActionTemplate === null) { //apparently no template was used, fall back to string
            $ActionTemplate = $this->getView()->getContainer();
        }

        $Theme = $this->getViewManager()->getCurrentTheme($this->getName());
        
        $Theme->set('body', $ActionTemplate);
        $this->executeView($Theme, $action);
        
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
        $result_view = $this->executeView($this->getView(), $action.'OnError');
        
        if ($result === false && $result_view === false) {
            return false;
        }

        if ($result === true || $result_view === true) {
            return true;
        }
        
        return null;
    }

    /**
     * @param $View
     * @param $action
     * @return bool|null
     */
    protected function executeView($View, $action)
    {
        if ($this->isCallable($View, $action)) {
            $result = $View->{$action}();
            $result = ($result !== false) ? true : $result;
            return $result;
        }
        
        return null;
    }
    
    /**
     * @return View\Interfaces\View
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
     * @return View\Interfaces\TemplateContainer
     */
    public function getActionTemplate()
    {
        return $this->getView()->getTemplate($this->action, $this->getView()->getData());
    }

    /**
     * @inheritdoc
     */
    public function showException(\Exception $Exception)
    {
        $message = $Exception->getMessage();
        $code = $Exception->getCode();
        if ($Exception instanceof Http\Exception) {
            $code = $Exception->getHttpMessage()->getCode();
        }

        $Theme = $this->getViewManager()->getCurrentTheme('Error');
        $Theme->set('error', $message);
        $data = $this->arrayMergeDefault($Theme->getData(), $this->getView()->getData());
        $Theme->setData($data);
        $this->getView()->setContainer($Theme->getContainer());
        $this->getViewManager()->compileView(null, $this->getView());
        $this->getResponse()->setData((string) $this->getView()->getContainer());

        $this->getResponse()->setStatusCode($code);
        $this->getResponse()->setStatusMessage($message);
        $this->response();
    }
    
    public function redirect($name, $query=[])
    {
        $Item = $this->getConfigManager()->getConfigByName('router')->getItemByName($name);
        $Item->compileUrl($query);
        
        if ($Item === null) {
            throw new Exception\Controller('Invalid router config name: "%s"', $name);
        }
        
        $this->getResponse()->setHeader('refresh', '1; url='.$Item->getParsedUrl());
    }
}