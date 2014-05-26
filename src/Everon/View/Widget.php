<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Everon\View;

use Everon\Dependency;
use Everon\Helper;
use Everon\View\Interfaces;
use Everon\View\Dependency\Injection\ViewManager as ViewManagerDependency;

abstract class Widget implements Interfaces\Widget
{
    use ViewManagerDependency;

    use Helper\ToString;
    use Helper\String\LastTokenToName;

    protected $name;

    protected $populated = null;

    /**
     * @var Interfaces\View
     */
    protected $View;

    protected abstract function populate();
    

    public function __construct(Interfaces\View $View)
    {
        $this->name = $this->stringLastTokenToName(get_called_class());
        $this->View = $View;
    }

    /**
     * @param Interfaces\View $View
     */
    public function setView($View)
    {
        $this->View = $View;
    }

    /**
     * @return Interfaces\View
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
            $this->populate();
            $this->populated = true;
        }
        
        $Tpl = $this->getView()->getTemplate('index', $this->getView()->getData());
        $this->getView()->setContainer($Tpl);
        
        $this->getViewManager()->compileView('', $this->getView());
        return (string) $this->getView()->getContainer();
    }

}