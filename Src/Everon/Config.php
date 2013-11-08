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

use Everon\Helper;
use Everon\Exception;


class Config implements Interfaces\Config, Interfaces\Arrayable
{
    use Dependency\Injection\Factory;

    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;
    use Helper\ArrayMergeDefault;
    use Helper\ToArray;

    /**
     * @var array
     */
    protected $data = [];
    
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
     * @param Interfaces\ConfigLoaderItem $ConfigLoaderItem
     * @param callable $Compiler
     */
    public function __construct($name, Interfaces\ConfigLoaderItem $ConfigLoaderItem, \Closure $Compiler)
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
            $RouteItem = $this->buildItem($item_name, $config_data); 
            $this->items[$item_name] = $RouteItem;

            $DefaultOrFirstItem = (is_null($DefaultOrFirstItem)) ? $RouteItem : $DefaultOrFirstItem;
            if ($RouteItem->isDefault()) {
                $this->setDefaultItem($RouteItem);
            }
        }
        
        if (is_null($this->DefaultItem)) {
            $this->setDefaultItem($DefaultOrFirstItem);
        }
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
     * @param $name
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
     * @param $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param mixed $Default
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
     * @param string $name
     * @return Interfaces\ConfigItem
     */
    public function getItemByName($name)
    {
        $name = (string) $name;
        if (is_null($this->items)) {
            $this->initItems();
        }
        
        $this->assertIsArrayKey($name, $this->items, 'Invalid config item name: "%s"');
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
    
    public function recompile($data)
    {
        $this->Compiler->__invoke($data);
        return $data;
    }

    /**
     * @return callable|null  Wrapped Interfaces\ConfigExpressionMatcher
     */
    public function getCompiler()
    {
        return $this->Compiler;
    }

    /**
     * @param \Closure $Compiler Wrapped Interfaces\ConfigExpressionMatcher
     */
    public function setCompiler(\Closure $Compiler)
    {
        $this->Compiler = $Compiler;
    }
}