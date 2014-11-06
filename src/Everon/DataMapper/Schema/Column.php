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

use Everon\Dependency;
use Everon\DataMapper\Exception;
use Everon\DataMapper\Interfaces\Schema;
use Everon\Helper;

abstract class Column implements Schema\Column 
{
    use Dependency\Injection\Factory;
    
    use Helper\DateFormatter;
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

    /**
     * @var \Closure
     */
    protected $Validator = null;

    protected $schema = null;
    
    protected $table = null;

    /**
     * @var string
     */
    protected $database_timezone = null;

    /**
     * Validators should ensure that the values can be passed to sql
     * 
     * @param array $data
     * @param array $primary_key_list
     * @param array $unique_key_list
     * @param array $foreign_key_list
     * @return mixed|void
     * @throws \Everon\DataMapper\Exception\Column
     */
    abstract protected function init(array $data, array $primary_key_list, array $unique_key_list, array $foreign_key_list);

    /**
     * @param $database_timezone
     * @param array $data
     * @param array $primary_key_list
     * @param array $unique_key_list
     * @param array $foreign_key_list
     */
    public function __construct($database_timezone, array $data, array $primary_key_list, array $unique_key_list, array $foreign_key_list)
    {
        $this->database_timezone = $database_timezone;
        $this->init($data, $primary_key_list, $unique_key_list, $foreign_key_list);
    }

    protected function getToString()
    {
        return (string) $this->name;
    }

    /**
     * @param $name
     * @param $data
     * @return bool
     */
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
    public function markAsPk()
    {
        $this->is_pk = true;
    }

    /**
     * @inheritdoc
     */
    public function unMarkAsPk()
    {
        $this->is_pk = false;
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
    public function getValidator()
    {
        return $this->Validator;
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
     * @inheritdoc
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }
    
    /**
     * @inheritdoc
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * @inheritdoc
     */
    public function setIsNullable($is_nullable)
    {
        $this->is_nullable = $is_nullable;
    }

    /**
     * @inheritdoc
     */
    public function setIsPk($is_pk)
    {
        $this->is_pk = $is_pk;
    }

    /**
     * @inheritdoc
     */
    public function setIsUnique($is_unique)
    {
        $this->is_unique = $is_unique;
    }

    /**
     * @inheritdoc
     */
    public function setLength($length)
    {
        $this->length = $length;
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
    public function setPrecision($precision)
    {
        $this->precision = $precision;
    }

    /**
     * @inheritdoc
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @inheritdoc
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function setValidator($validation_rules)
    {
        $this->Validator = $validation_rules;
    }

    /**
     * @inheritdoc
     */
    public function validateColumnValue($value)
    {
        try {
            if ($this->getValidator() === null) {
                return $value; //validation is disabled
            }

            if ($this->isNullable() && $value === null) {
                return $value; //no need to validate anything
            }

            $display_value = ($value === null) ? 'NULL' : $value;
            
            $Validator = $this->getValidator();
            $validation_result = $Validator($value);
            if ($validation_result !== true) {
                throw new Exception\Column('Column: "%s@%s" failed to validate with value: "%s"', [$this->getTable(), $this->getName(), $display_value]);
            }

            return $value;
        }
        catch (\Exception $e) {
            throw new Exception\Column($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function getDataForSql($value)
    {
        if ($this->isNullable() && $value === null) {
            return $value;
        }

        if ($this->isPk() && $value === null) {
            return $value;
        }
        
        switch ($this->type) {
            case self::TYPE_INTEGER:
                return (int) $value;
                break;

            case self::TYPE_FLOAT:
                return (float) $value;
                break;
            
            case self::TYPE_BOOLEAN:
                return ((bool) $value) ? 't' : 'f';
                break;

            case self::TYPE_TIMESTAMP:
                /**
                 * @var \DateTime $value
                 */
                return $this->dateAsPostgreSql($value, $this->getFactory()->buildDateTimeZone($this->database_timezone));
                break;

            default:
                return $value;
                break;           
        }
    }

    /**
     * @inheritdoc
     */
    public function getColumnDataForEntity($value)
    {
        if ($this->isNullable() && $value === null) {
            return $value;
        }

        if ($this->isPk() && $value === null) {
            return $value;
        }
        
        switch ($this->type) {
            case self::TYPE_INTEGER:
                return (int) $value;
                break;

            case self::TYPE_FLOAT:
                return (float) $value;
                break;
            
            case self::TYPE_BOOLEAN:
                return $value === true;
                break;

            case self::TYPE_TIMESTAMP:
                if ($value instanceof \DateTime) {
                    return $value;
                }
                
                return $this->getDateTime($value, $this->database_timezone);
                break;

            default:
                return $value;
                break;
        }
    }
}