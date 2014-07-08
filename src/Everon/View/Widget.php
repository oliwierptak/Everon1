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
use Everon\Exception;

abstract class Widget implements Interfaces\Widget
{
    use ViewManagerDependency;
    use Dependency\Logger;

    use Helper\ToString;
    use Helper\String\LastTokenToName;
    use Dependency\Injection\ConfigManager;

    protected $name;

    protected $populated = null;
    
    protected $has_data = false;

    /**
     * @var Interfaces\View
     */
    protected $View;

    /**
     * @return boolean True if the data was loaded successfully
     */
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
    
    public function getUrl($name, $query=[], $get=[])
    {
        $Item = $this->getConfigManager()->getConfigByName('router')->getItemByName($name);
        if ($Item === null) {
            throw new Exception\Controller('Invalid router config name: "%s"', $name);
        }

        $Item->compileUrl($query);
        $url = $Item->getParsedUrl();

        $get_url = '';
        if (empty($get) === false) {
            $get_url = http_build_query($get);
            if (trim($get_url) !== '') {
                $get_url = '?'.$get_url;
            }
        }

        return $url.$get_url;
    }
}