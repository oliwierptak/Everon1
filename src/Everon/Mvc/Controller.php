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
abstract class Controller extends \Everon\Controller implements Interfaces\Controller
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

        $Theme = $this->getViewManager()->getCurrentTheme($this->getName());
        $data = $this->arrayMergeDefault($Theme->getData(), $this->getView()->getData());

        $ActionTemplate = $this->getView()->getTemplate($action, $data);
        if ($ActionTemplate === null) { //apparently no template was used, fall back to string
            $ActionTemplate = $this->getView()->getContainer();
        }

        $Theme->set('body', $ActionTemplate);
        $this->executeView($Theme, $action);
        
        //$data = $this->arrayMergeDefault($Theme->getData(), $ActionTemplate->getData());
        //$Theme->setData($data);
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
     * @param View\Interfaces\View $View
     * @param $action
     * @return bool
     */
    protected function executeView(View\Interfaces\View $View, $action)
    {
        $result = $View->execute($action);
        $result = ($result !== false) ? true : $result;
        return $result;
    }
    
    /**
     * @inheritdoc
     */
    public function getView()
    {
        return $this->getModule()->getViewByName($this->getName());
    }

    /**
     * @inheritdoc
     */
    public function setView(View\Interfaces\View $View)
    {
        $this->getModule()->setViewByViewName($View);
    }
    
    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->getDomainManager()->getModel($this->getName());
    }

    /**
     * @inheritdoc
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
        $this->getView()->set('body', '');
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

    /**
     * @inheritdoc
     */
    public function redirect($name, $query=[], $get=[])
    {
        $url = $this->getUrl($name, $query, $get);
        $this->getResponse()->setHeader('refresh', '1; url='.$url);
    }
}