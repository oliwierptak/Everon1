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

//todo: no class should rely on concrete classes, either make it abstract or remove config\router
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
        $filename = $ConfigLoaderItem->getFilename();
        $data = $ConfigLoaderItem->getData();
        
        $this->name = $name;
        $this->filename = $filename;
        $this->data = $data;
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

            //make sure everything has default
            $default = reset($data_processed);
            foreach ($data_processed as $name => $data) {
                $data_processed[$name] = $this->arrayMergeDefault($default, $data_processed[$name]);
            }
        }
        
        $this->data = $data_processed;
    }

    protected function initItems()
    {
        $this->processData();
        
        $DefaultOrFirstItem = null;
        foreach ($this->data as $item_name => $config_data) {
            $Item = $this->buildItem($item_name, $config_data); 
            $this->items[$item_name] = $Item;

            $DefaultOrFirstItem = (is_null($DefaultOrFirstItem)) ? $Item : $DefaultOrFirstItem;
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

    protected function buildItem($name, array $data)
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
     * @return mixed
     */
    public function getDefaultItem()
    {
        if (is_null($this->DefaultItem)) {
            $this->initItems();
        }
        
        return $this->DefaultItem;
    }

    /**
     * @return array
     */
    protected function getToArray()
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
            //return $this->getDefaultItem(); xxx
            return null;
        }
        
        return $this->items[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function itemExists($name)
    {
        if (is_null($this->items)) {
            $this->initItems();
        }
        
        return isset($this->items[$name]);
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
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
        $data = $Item->toArray();
        $this->go_path = [];
        
        return isset($data[$name]) ? $data[$name] : $default;
    }

    /**
     * @param $where
     * @return Interfaces\Config
     */
    public function go($where)
    {
        $this->go_path[] = $where; 
        return $this;
    }
    
    public function recompile($data) //todo: meh, use setItems(), decouple compiler
    {
        $this->Compiler->__invoke($data);
        return $data;
    }

    /**
     * @return callable|null  Wrapped Config\Interfaces\ExpressionMatcher
     */
    public function getCompiler()
    {
        return $this->Compiler;
    }

    /**
     * @param \Closure $Compiler Wrapped Config\Interfaces\ExpressionMatcher
     */
    public function setCompiler(\Closure $Compiler)
    {
        $this->Compiler = $Compiler;
    }
}