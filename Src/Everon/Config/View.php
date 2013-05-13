<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config;

use Everon\Dependency;
use Everon\Helper;
use Everon\Interfaces;

class View extends \Everon\Config implements Interfaces\ConfigView
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\ConfigManager;
    
    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;

    /**
     * @var array
     */
    protected $pages = null;

    /**
     * @var Interfaces\ConfigItemView
     */
    protected $DefaultPage = null;


    protected function initPages()
    {
        $default_or_first_item = null;
        foreach ($this->getData() as $page_name => $config_data) {
            $config_data['view_name'] = $page_name;
            $RouteItem = $this->getFactory()->buildConfigItemView($config_data);
            $this->pages[$page_name] = $RouteItem;

            $default_or_first_item = (is_null($default_or_first_item)) ? $RouteItem : $default_or_first_item;
            if ($RouteItem->isDefault()) {
                $this->setDefaultPage($RouteItem);
            }
        }

        if (is_null($this->DefaultPage)) {
            $this->setDefaultPage($default_or_first_item);
        }
    }

    /**
     * @param \Everon\Interfaces\ConfigItemView $ViewItem
     */
    public function setDefaultPage(Interfaces\ConfigItemView $ViewItem)
    {
        $this->DefaultPage = $ViewItem;
    }

    /**
     * @return Interfaces\ConfigItemView
     */
    public function getDefaultPage()
    {
        if (is_null($this->DefaultPage)) {
            $this->initPages();
        }

        return $this->DefaultPage;
    }

    /**
     * @return array|null
     */
    public function getPages()
    {
        if (is_null($this->pages)) {
            $this->initPages();
        }
        
        return $this->pages;
    }

    /**
     * @param string $page_name
     * @return Interfaces\ConfigItemView
     */
    public function getPageByName($page_name)
    {
        if (is_null($this->pages)) {
            $this->initPages();
        }

        $this->assertIsArrayKey($page_name, $this->pages, 'Invalid page name: "%s"');
        return $this->pages[$page_name];
    }

}
