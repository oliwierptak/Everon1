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
use Everon\Interfaces;
use Everon\Helper;
use Everon\Http;
use Everon\Mvc;
use Everon\View;

/**
  * @method \Everon\Module\Interfaces\Mvc getModule()
 */
abstract class Controller extends Http\Controller implements Mvc\Interfaces\Controller
{
    use Dependency\Injection\Factory;
    use Domain\Dependency\Injection\DomainManager;
    use View\Dependency\Injection\ViewManager;

    use Helper\Arrays;

    /**
     * @var string
     */
    protected $view_name = null;

    /**
     * @var string
     */
    protected $layout_name = null;

    /**
     * @var View\Interfaces\TemplateContainer
     */
    protected $ActionTemplate = null;


    /**
     * @param \Everon\Module\Interfaces\Module $Module
     */
    public function __construct(\Everon\Module\Interfaces\Module $Module)
    {
        parent::__construct($Module);

        if ($this->view_name === null) {
            $this->view_name = $this->getName();
        }

        if ($this->layout_name === null) {
            $this->layout_name = $this->getName();
        }
    }

    /**
     * @param $action
     * @param $result
     */
    protected function prepareResponse($action, $result)
    {
        if ($result) {
            $this->executeView($this->getView(), $action);
        }
        else {
            $this->executeView($this->getView(), $action.'onError');
        }

        $Layout = $this->getViewManager()->createLayout($this->getLayoutName());
        $data = $this->arrayMergeDefault($Layout->getData(), $this->getView()->getData()); //import view variables into template
        $Layout->setData($data);

        if ($result) {
            $ActionTemplate = $this->getView()->getTemplate($action, $data);
            if ($ActionTemplate !== null && $this->is_redirecting === false) {
                $this->getView()->setContainer($ActionTemplate);
                $Layout->set('body', $this->getView());
                $Layout->set('flash_message', $this->getFlashMessage());
                $this->resetFlashMessage();
            }
        }

        $this->executeView($Layout, $action);
        $this->getViewManager()->compileView($action, $Layout);
        $this->getResponse()->setData($Layout->getContainer()->getCompiledContent());

        if ($this->getResponse()->wasStatusSet() === false) {//DRY
            $Ok = new Http\Message\Ok();
            $this->getResponse()->setStatusCode($Ok->getCode());
            $this->getResponse()->setStatusMessage($Ok->getMessage());
        }
    }

    protected function response()
    {
        echo $this->getResponse()->toHtml();
    }

    /**
     * @param $action
     * @return bool
     */
    /*
    
    call only view onError actions when error has occurred 
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
    }*/

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
     * @param string $view_name
     */
    public function setViewName($view_name)
    {
        $this->view_name = $view_name;
    }

    /**
     * @return string
     */
    public function getViewName()
    {
        return $this->view_name;
    }

    /**
     * @param string $layout_name
     */
    public function setLayoutName($layout_name)
    {
        $this->layout_name = $layout_name;
    }

    /**
     * @return string
     */
    public function getLayoutName()
    {
        return $this->layout_name;
    }

    /**
     * @inheritdoc
     */
    public function getView()
    {
        return $this->getModule()->getViewByName($this->getLayoutName(), $this->getViewName());
    }

    /**
     * @inheritdoc
     */
    public function setView(View\Interfaces\View $View)
    {
        $this->getModule()->setViewByViewName($this->getLayoutName(), $View);
    }

    /**
     * @inheritdoc
     */
    public function getActionTemplate()
    {
        if ($this->ActionTemplate === null) {
            $this->ActionTemplate = $this->getView()->getTemplate($this->action, $this->getView()->getData());
        }
        return $this->ActionTemplate;
    }

    /**
     * @inheritdoc
     */
    public function setActionTemplate(View\Interfaces\TemplateContainer $Template)
    {
        $this->ActionTemplate = $Template;
    }

    /**
     * @inheritdoc
     */
    public function showException(\Exception $Exception)
    {
        $layout_name = $this->getConfigManager()->getConfigValue('everon.error_handler.view');
        $Layout = $this->getViewManager()->createLayout($layout_name);
        $Layout->set('error', $Exception->getMessage());
        $Layout->execute('show');

        $this->getViewManager()->compileView('', $Layout);

        $content = (string) $Layout->getContainer()->getCompiledContent();
        $this->getResponse()->setData($content);

        $this->response();
    }

    /**
     * @inheritdoc
     */
    public function showValidationErrors($errors=null)
    {
        /**
         * @var \Everon\View\Interfaces\View $ErrorView
         * @var \Everon\Mvc\Interfaces\Controller $Controller
         */
        $error_view = $this->getConfigManager()->getConfigValue('everon.error_handler.view', null);
        $error_form_validation_error_template = $this->getConfigManager()->getConfigValue('everon.error_handler.validation_error_template', null);

        $ErrorView = $this->getViewManager()->createLayout($error_view);
        $ErrorView->set('validation_errors', $errors ?: $this->getRouter()->getRequestValidator()->getErrors());
        $ErrorView->execute('show');

        $Tpl = $ErrorView->getTemplate($error_form_validation_error_template, $ErrorView->getData());
        if ($Tpl === null) {
            $this->getLogger()->error('Invalid error template: '.$error_view.'@'.$error_form_validation_error_template);
        }

        $this->getView()->set('error', $Tpl);

        $BadRequest = new Http\Message\BadRequest();
        $this->getResponse()->setStatusCode($BadRequest->getCode());
        $this->getResponse()->setStatusMessage($BadRequest->getMessage());

        $this->were_error_handled = true;
    }

    /**
     * @inheritdoc
     */
    public function error($msg, array $parameters=[]) //xxx
    {
        if (empty($parameters) === false) {
            $msg = vsprintf($msg, $parameters);
        }
        $this->showValidationErrors(['error' => $msg]);
    }

    /**
     * @inheritdoc
     */
    public function redirect($name, $query=[], $get=[])
    {
        $this->is_redirecting = true;
        $url = $this->getUrl($name, $query, $get);
        $this->getResponse()->setHeader('location', $url);
    }
}