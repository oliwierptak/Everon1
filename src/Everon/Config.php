<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

use Everon\Config\Interfaces;
use Everon\Helper;
use Everon\Exception;


class Config implements \Everon\Interfaces\Config
{
    use Dependency\Injection\Factory;
    
    use Helper\Arrays;
    use Helper\Exceptions;
    use Helper\Asserts\IsArrayKey;
    use Helper\ToArray;

    protected $name = null;

    protected $filename = '';

    /**
     * @var array
     */
    protected $go_path = [];

    /**
     * @var mixed
     */
    protected $default_item_name = null;

    /**
     * @var array
     */
    protected $items = null;


    public function __construct($name, $filename, array $data)
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->data = $data;
    }

    protected function initItems()
    {
        if ($this->isEmpty()) {
            return;
        }

        $DefaultOrFirstItem = null;
        foreach ($this->data as $item_name => $config_data) {
            $Item = $this->buildItem($item_name, $config_data);
            $this->items[$item_name] = $Item;

            $DefaultOrFirstItem = ($DefaultOrFirstItem === null) ? $Item : $DefaultOrFirstItem;
            if ($Item->isDefault()) {
                $this->setDefaultItemName($Item->getName());
            }
        }

        if (is_null($this->default_item_name)) {
            $DefaultOrFirstItem->setIsDefault(true);
            $this->setDefaultItemName($DefaultOrFirstItem->getName());
        }

        $this->data = null; //only getItems() from now on
    }

    /**
     * @param $name
     * @param array $data
     * @return Interfaces\Item|Config\Item
     */
    public function buildItem($name, array $data)
    {
        return $this->getFactory()->buildConfigItem($name, $data);
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @inheritdoc
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultItemName($name)
    {
        $this->default_item_name = $name;
    }

    /**
     * @return string
     */
    public function getDefaultItemName()
    {
        return $this->default_item_name;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultItem()
    {
        if (is_null($this->items)) {
            $this->initItems();
        }
        
        if ($this->default_item_name === null && $this->isEmpty() === false || (@isset($this->items[$this->default_item_name]) === false )) {
            throw new Exception\Config('Default config item not defined for config: "%s"', $this->getName());
        }

        return $this->items[$this->default_item_name];
    }

    /**
     * @param bool $deep
     * @return array|null
     */
    protected function getToArray($deep=false)
    {
        return $this->getItems();
    }

    /**
     * @return array|null
     */
    public function getItems()
    {
        if (is_null($this->items)) {
            $this->initItems();
        }

        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param string $name
     * @return Config\Interfaces\Item
     */
    public function getItemByName($name)
    {
        $name = (string) $name;
        if (is_null($this->items)) {
            $this->initItems();
        }

        if (isset($this->items[$name]) === false) {
            return null;
        }

        return $this->items[$name];
    }

    /**
     * @inheritdoc
     */
    public function itemExists($name)
    {
        if (is_null($this->items)) {
            $this->initItems();
        }

        return isset($this->items[$name]);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return ($this->data === null || empty($this->data));
    }

    /**
     * @inheritdoc
     */
    public function get($name, $default=null)
    {
        $section = array_shift($this->go_path);

        if ($section === null) {
            $Item = $this->getItemByName($name);
            if ($Item === null) {
                return $default;
            }
            return $Item->toArray();
        }

        $Item = $this->getItemByName($section);
        if ($Item === null) {
            return $default;
        }

        $data = $Item->toArray();
        $this->go_path = [];

        return isset($data[$name]) ? $data[$name] : $default;
    }

    /**
     * @inheritdoc
     */
    public function go($where)
    {
        $this->go_path[] = $where;
        return $this;
    }
}