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

abstract class Widget implements Interfaces\Widget
{
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Logger;
    use Dependency\Injection\Response;
    use Dependency\Injection\Request;
    use Dependency\Injection\Factory;

    use Helper\ToString;
    use Helper\String\LastTokenToName;

    protected $name;

    protected $data;

    /**
     * @var Interfaces\View
     */
    protected $View;

    protected abstract function populate();
    

    public function __construct()
    {
        $this->data = null;
        $this->name = $this->stringLastTokenToName(get_class($this));
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
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        if ($this->data === null) {
            $this->populate();
        }
        return $this->data;
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
        $Tpl = $this->getView()->getTemplate('Index', $this->getData());

        $this->getView()->setContainer($Tpl);
        $this->getViewManager()->compileView('', $this->getView());

        return (string) $this->getView()->getContainer();
    }


}