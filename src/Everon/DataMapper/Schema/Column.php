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
    use Helper\ToString;

    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_JSON = 'json';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_POINT = 'point';
    const TYPE_POLYGON = 'polygon';
    
    
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
    
    protected $table = null;


    abstract protected function init(array $data, array $primary_key_list, array $unique_key_list, array $foreign_key_list);
    

    public function __construct(array $data, array $primary_key_list, array $unique_key_list, array $foreign_key_list)
    {
        $this->init($data, $primary_key_list, $unique_key_list, $foreign_key_list);
    }

    protected function getToString()
    {
        return (string) $this->name;
    }

    protected function hasInfo($name, $data)
    {
        return isset($data[$name]);
    }

    /**
     * @inheritdoc
     */
    public function isPk()
    {
        return $this->is_pk;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @inheritdoc
     */
    public function isNullable()
    {
        return $this->is_nullable;
    }

    /**
     * @inheritdoc
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @inheritdoc
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @inheritdoc
     */
    public function getValidationRules()
    {
        return $this->validation_rules;
    }

    /**
     * @inheritdoc
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @inheritdoc
     */
    public function toArray($deep=false)
    {
        return get_object_vars($this);
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * @param boolean $is_nullable
     */
    public function setIsNullable($is_nullable)
    {
        $this->is_nullable = $is_nullable;
    }

    /**
     * @param boolean $is_pk
     */
    public function setIsPk($is_pk)
    {
        $this->is_pk = $is_pk;
    }

    /**
     * @param boolean $is_unique
     */
    public function setIsUnique($is_unique)
    {
        $this->is_unique = $is_unique;
    }

    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param int $precision
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;
    }

    /**
     * @param string $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param string $validation_rules
     */
    public function setValidationRules($validation_rules)
    {
        $this->validation_rules = $validation_rules;
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