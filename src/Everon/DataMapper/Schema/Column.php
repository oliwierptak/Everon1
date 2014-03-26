<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema;

use Everon\Helper;
use Everon\DataMapper\Exception;
use Everon\DataMapper\Interfaces\Schema;

abstract class Column implements Schema\Column 
{
    use Helper\Immutable;
    use Helper\ToString;

    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_JSON = 'json';
    
    
    protected $is_pk = null;
    
    protected $is_unique = null;

    protected $name = null;

    protected $type = null;
    
    protected $length = null;
    
    protected $precision = null;
    
    protected $is_nullable = null;
    
    protected $default = null;
    
    protected $encoding = null;
    
    protected $validation_rules = null;

    protected $schema = null;


    abstract protected function init(array $data, array $primary_key_list, array $unique_key_list, array $foreign_key_list);
    

    public function __construct(array $data, array $primary_key_list, array $unique_key_list, array $foreign_key_list)
    {
        $this->init($data, $primary_key_list, $unique_key_list, $foreign_key_list);
        $this->lock();
    }

    protected function getToString()
    {
        return (string) $this->name;
    }

    protected function hasInfo($name, $data)
    {
        return isset($data[$name]);
    }

    public function isPk()
    {
        return $this->is_pk;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function isNullable()
    {
        return $this->is_nullable;
    }

    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return null
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return array
     */
    public function getValidationRules()
    {
        return $this->validation_rules;
    }
    
    public function toArray($deep=false)
    {
        return get_object_vars($this);
    }

    /**
     * @param $is_pk
     */
    public function updatePkStatus($is_pk)
    {
        $this->unlock();
        $this->is_pk = $is_pk;
        $this->lock();
    }

    /**
     * @inheritdoc
     */
    public function validateColumnValue($value)
    {
        try {
            if ($this->getValidationRules() === null) {
                return $value; //validation is disabled
            }

            if ($this->isNullable() && $value === null) {
                return $value;
            }
            
            $validation_result = filter_var_array([$this->getName() => $value], $this->getValidationRules());
            $display_value = $value === null ? 'NULL' : $value;

            if (($validation_result === false || $validation_result === null)) {
                throw new Exception\Column('Column: "%s" failed to validate with value: "%s"', [$this->getName(), $display_value]);
            }

            $value = $validation_result[$this->getName()];
            if ($value === false) {
                throw new Exception\Column('Column: "%s" failed to validate with value: "%s"', [$this->getName(), $display_value]);
            }

            return $value;
        }
        catch (\Exception $e) {
            throw new Exception\Column($e->getMessage());
        }
    }
}