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

use Everon\Helper;
use Everon\Interfaces;

class Item implements Interfaces\ConfigItem
{
    const PROPERTY_NAME = '____name';
    const DEFAULT_NAME = '_default';

    use Helper\Asserts;
    use Helper\Asserts\IsStringAndNonEmpty;
    use Helper\ToArray;
    
    protected $name = null;
    
    /**
     * @var boolean
     */
    protected $is_default = false;
    

    public function __construct(array $data, array $defaults=[])
    {
        $empty_defaults = [
            self::DEFAULT_NAME => false,
        ];

        $empty_defaults = array_merge($empty_defaults, $defaults);

        $this->data = array_merge($empty_defaults, $data);
        $this->init();
    }
    
    protected function init()
    {
        $this->validateData($this->data);
        $this->setName($this->data[self::PROPERTY_NAME]);
        $this->setIsDefault($this->data[self::DEFAULT_NAME]);
        unset($this->data[self::DEFAULT_NAME]);
    }
    
    /**
     * @param array $data
     */
    public function validateData(array $data)
    {
        $this->assertIsStringAndNonEmpty($data[self::PROPERTY_NAME], 'Invalid item name: "%s"', 'ConfigItem');
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

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->is_default;
    }

    /**
     * @param boolean $is_default
     */
    public function setIsDefault($is_default)
    {
        $this->is_default = (bool) $is_default;
    }
}
