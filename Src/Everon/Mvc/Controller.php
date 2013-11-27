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

use Everon\Interfaces;
use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Http;

abstract class Controller extends \Everon\Controller implements Interfaces\MvcController
{
    use Dependency\Injection\ViewManager;
    use Dependency\Injection\ModelManager;
    use Dependency\Injection\Environment;
    
    use Helper\Arrays;
    use Helper\IsIterable;
    use Helper\String\StartsWith;
    
    protected $CacheLoader = null;

    /**
     * @return Interfaces\View
     */
    public function getView()
    {
        return $this->getViewManager()->getView($this->getName());
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->getModelManager()->getModel($this->getName());
    }

    protected function prepareResponse($action)
    {
        if ($this->isCallable($this->getView(), $action)) {
            $this->getView()->{$action}();
        }

        $PageTemplate = $this->getView()->getViewTemplateByAction(
            $action, $this->getViewManager()->getDefaultView()->getViewTemplate()
        );

        if ($PageTemplate === null) {
            throw new Http\Exception\NotFound('Page template: "%s/%s" not found', [$this->getName(),$action]);
        }
        /*
        $filename = basename($PageTemplate->getTemplateFile());
        $this->CacheLoader = new \SplFileInfo($this->getEnvironment()->getCache().'view'.DIRECTORY_SEPARATOR.''.$filename);
        $this->CacheLoaderData = new \SplFileInfo($this->getEnvironment()->getCache().'view'.DIRECTORY_SEPARATOR.''.$filename.'.json');
        */
        
        $this->getView()->setContainer($PageTemplate);
        $this->getViewManager()->compileView($this->getView());
        
        $content = (string) $PageTemplate;
        $this->getResponse()->setData($content);
    }

    protected function response()
    {
        echo $this->getResponse()->toHtml();
    }

}