<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Widget;

use Everon\Application;
use Everon\Dependency;
use Everon\Domain;
use Everon\Helper;
use Everon\View;
use Everon\Exception;

abstract class AbstractWidget implements View\Interfaces\Widget
{
    use Dependency\Injection\Router;
    use Domain\Dependency\Injection\DomainManager;
    use View\Dependency\Injection\ViewManager;
    
    use Helper\GetUrl;
    use Helper\String\LastTokenToName;
    use Helper\ToString;

    protected $name;

    protected $populated = null;
    
    protected $has_data = false;

    /**
     * @var View\Interfaces\View
     */
    protected $View;

    /**
     * @return boolean True if the data was loaded successfully
     */
    protected abstract function populate();
    

    public function __construct(View\Interfaces\View $View)
    {
        $this->name = $this->stringLastTokenToName(get_called_class());
        $this->View = $View;
    }

    /**
     * @param View\Interfaces\View $View
     */
    public function setView($View)
    {
        $this->View = $View;
    }

    /**
     * @return View\Interfaces\View
     */
    public function getView()
    {
        return $this->View;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function render()
    {
        if ($this->populated !== true) {
            $this->has_data = $this->populate() !== false;
            $this->populated = true;
        }
        
        if ($this->hasData() === false) {
            return '';
        }
        
        $Tpl = $this->getView()->getTemplate('index', $this->getView()->getData());
        $this->getView()->setContainer($Tpl);
        
        $this->getViewManager()->compileView('', $this->getView());
        return (string) $this->getView()->getContainer();
    }

    /**
     * @param boolean $has_data
     */
    public function setHasData($has_data)
    {
        $this->has_data = $has_data;
    }

    /**
     * @return boolean
     */
    public function hasData()
    {
        return $this->has_data;
    }
}