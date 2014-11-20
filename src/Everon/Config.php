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

    protected $data_processed = false;

    /**
     * @var mixed
     */
    protected $DefaultItem = null;

    /**
     * @var \Closure
     */
    protected $Compiler = null;

    /**
     * @var array
     */
    protected $items = null;

    protected $inheritance_symbol = '<';


    /**
     * @param $name
     * @param Config\Interfaces\LoaderItem $ConfigLoaderItem
     * @param callable $Compiler
     */
    public function __construct($name, Config\Interfaces\LoaderItem $ConfigLoaderItem, \Closure $Compiler)
    {
        $this->name = $name;
        $this->filename = $ConfigLoaderItem->getFilename();
        $this->data = $ConfigLoaderItem->getData();
        $this->Compiler = $Compiler;
    }

    protected function processData()
    {
        if ($this->data_processed === true) {
            return;
        }

        $this->data_processed = true;
        $HasInheritance = function($value) {
            return strpos($value, $this->inheritance_symbol) !== false;
        };

        $use_inheritance = false;
        foreach ($this->data as $name => $data) {
            if ($HasInheritance($name) === true) {
                $use_inheritance = true;
                break;
            }
        }

        if ($use_inheritance === false) {
            return;
        }

        $inheritance_list = [];
        $data_processed = [];
        foreach ($this->data as $name => $data) {
            if ($HasInheritance($name) === true) {
                list($for, $from) = explode($this->inheritance_symbol, $name);
                $for = trim($for);
                $from = trim($from);
                $inheritance_list[$for] = $from;
                $data_processed[$for] = $data;
            }
            else {
                $data_processed[$name] = $data;
            }
        }

        if (empty($inheritance_list) === false) {
            foreach ($inheritance_list as $for => $from) {
                $this->assertIsArrayKey($for, $data_processed, 'Undefined config for section: "%s"');
                $this->assertIsArrayKey($from, $data_processed, 'Undefined config from section: "%s"');
                $data_processed[$for] = $this->arrayMergeDefault($data_processed[$from], $data_processed[$for]);
            }
        }

        $this->data = $data_processed;
    }

    protected function initItems()
    {
        if ($this->isEmpty()) {
            return;
        }

        $this->processData();

        $DefaultOrFirstItem = null;
        foreach ($this->data as $item_name => $config_data) {
            $Item = $this->buildItem($item_name, $config_data);
            $this->items[$item_name] = $Item;

            $DefaultOrFirstItem = ($DefaultOrFirstItem === null) ? $Item : $DefaultOrFirstItem;
            if ($Item->isDefault()) {
                $this->setDefaultItem($Item);
            }
        }

        if (is_null($this->DefaultItem)) {
            $DefaultOrFirstItem->setIsDefault(true);
            $this->setDefaultItem($DefaultOrFirstItem);
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
    public function setDefaultItem($Default)
    {
        $this->DefaultItem = $Default;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultItem()
    {
        if (is_null($this->DefaultItem)) {
            $this->initItems();
        }

        if ($this->DefaultItem === null && $this->isEmpty() === false) {
            throw new Exception\Config('Default config item not defined for config: "%s"', $this->getName());
        }

        return $this->DefaultItem;
    }

    /**
     * @return array
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

    /**
     * @inheritdoc
     */
    public function recompile($data)
    {
        $this->Compiler->__invoke($data);
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getCompiler()
    {
        return $this->Compiler;
    }

    /**
     * @inheritdoc
     */
    public function setCompiler(\Closure $Compiler)
    {
        $this->Compiler = $Compiler;
    }

}