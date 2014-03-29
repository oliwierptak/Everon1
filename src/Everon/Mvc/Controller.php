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
use Everon\Exception;
use Everon\Helper;
use Everon\Http;

/**
 * @method Http\Interfaces\Response getResponse()
 */
abstract class Controller extends \Everon\Controller implements Interfaces\Controller
{
    use \Everon\Domain\Dependency\Injection\DomainManager;
    use Dependency\Injection\Environment;
    use Dependency\Injection\Factory;
    use \Everon\Module\Dependency\Injection\ModuleManager;
    use Dependency\Injection\ViewManager;

    use Helper\Arrays;
    use Helper\IsIterable;
    use Helper\String\StartsWith;
    
    
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

    protected function prepareResponse($action, $result)
    {
        if ($result && $this->isCallable($this->getView(), $action)) {
            $this->getView()->{$action}();
        }

        $ActionTemplate = $this->getView()->getTemplate($action, $this->getView()->getData());
        if ($ActionTemplate === null) { //apparently no template was used, fall back to string
            $ActionTemplate = $this->getView()->getContainer();
        }

        $Theme = $this->getViewManager()->getCurrentTheme();
        $Theme->set('View.body', $ActionTemplate);
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
     * @inheritdoc
     */
    public function showException(\Exception $Exception, $code=400)
    {
        $Theme = $this->getViewManager()->getCurrentTheme();
        $Theme->set('View.error', $Exception->getMessage());
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
}