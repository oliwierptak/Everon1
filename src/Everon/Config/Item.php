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

use Everon\Exception;
use Everon\Helper;

class Item implements Interfaces\Item
{
    const PROPERTY_DEFAULT = '_default';
    const PROPERTY_NAME = '____name';

    use Helper\Exceptions;
    use Helper\Asserts\IsStringAndNotEmpty;
    use Helper\ToArray;
    
    protected $name = null;
    
    /**
     * @var boolean
     */
    protected $is_default = false;
    

    public function __construct(array $data, array $defaults=[])
    {
        $empty_defaults = [
            static::PROPERTY_DEFAULT => false,
            static::PROPERTY_NAME => null,
        ];

        $empty_defaults = array_merge($empty_defaults, $defaults);

        $this->data = array_merge($empty_defaults, $data);
        $this->init();
    }
    
    protected function init()
    {
        $this->validateData($this->data);
        $this->setName($this->data[static::PROPERTY_NAME]);
        unset($this->data[static::PROPERTY_NAME]);
        $this->setIsDefault($this->data[static::PROPERTY_DEFAULT]);
        unset($this->data[static::PROPERTY_DEFAULT]);
    }
    
    /**
     * @param array $data
     */
    public function validateData(array $data)
    {
        $this->assertIsStringAndNonEmpty((string) @$data[static::PROPERTY_NAME], 'Invalid item name: "%s"', 'ConfigItem');
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
