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


    /**
     * @param array $data
     * @param array $defaults
     */
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
     * @inheritdoc
     */
    public function validateData(array $data)
    {
        $this->assertIsStringAndNonEmpty((string) @$data[static::PROPERTY_NAME], 'Invalid item name: "%s"', 'ConfigItem');
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function isDefault()
    {
        return $this->is_default;
    }

    /**
     * @inheritdoc
     */
    public function setIsDefault($is_default)
    {
        $this->is_default = (bool) $is_default;
    }

    /**
     * @inheritdoc
     */
    public function getValueByName($name, $default=null)
    {
        if (isset($this->data[$name]) === false) {
            return $default;
        }
        
        return $this->data[$name];
    }

    /**
     * @inheritdoc
     */
    public function setValueByName($name, $value)
    {
        return $this->data[$name] = $value;
    }

    public static function __set_state(array $parameters)
    {
        /**
         * @var Interfaces\Item $Item
         */
        $defaults[self::PROPERTY_NAME] = $parameters['name'];
        $defaults[self::PROPERTY_DEFAULT] = $parameters['is_default'];
        
        $Item = new static($parameters['data'], $defaults);
        
        return $Item;
    }
}
