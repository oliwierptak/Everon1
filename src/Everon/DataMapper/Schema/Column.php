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
use Everon\DataMapper\Interfaces;
use Everon\Helper;

abstract class Column implements Interfaces\Schema\Column 
{
    use Dependency\Factory;
    
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
     * @var Interfaces\Schema\Column\Validator
     */
    protected $Validator = null;

    protected $schema = null;
    
    protected $table = null;
    
    /**
     * @var string
     */
    protected $database_timezone = null;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $ColumnValidators = null;

    /**
     * Initialized Column data and validators. Validators should ensure that the values can be passed to sql
     *
     */
    abstract protected function init();


    /**
     * @param \Everon\Interfaces\Factory $Factory
     * @param $ColumnValidators
     * @param $database_timezone
     * @param array $data
     * @param $is_pk
     * @param $is_unique
     */
    public function __construct(\Everon\Interfaces\Factory $Factory, $ColumnValidators, $database_timezone, array $data, $is_pk, $is_unique)
    {
        static::$Factory = $Factory;
        $this->ColumnValidators = $ColumnValidators;
        $this->database_timezone = $database_timezone;
        $this->ColumnInfo = new Helper\PopoProps($data);
        $this->is_pk = $is_pk;
        $this->is_unique = $is_unique || $this->is_pk;
    }

    protected function getToString()
    {
        $this->init();
        return (string) $this->name;
    }

    /**
     * @inheritdoc
     */
    public function isPk()
    {
        $this->init();
        return $this->is_pk;
    }

    /**
     * @inheritdoc
     */
    public function markAsPk()
    {
        $this->init();
        $this->is_pk = true;
    }

    /**
     * @inheritdoc
     */
    public function unMarkAsPk()
    {
        $this->init();
        $this->is_pk = false;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        $this->init();
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        $this->init();
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getLength()
    {
        $this->init();
        return $this->length;
    }

    /**
     * @inheritdoc
     */
    public function isNullable()
    {
        $this->init();
        return $this->is_nullable;
    }

    /**
     * @inheritdoc
     */
    public function getDefault()
    {
        $this->init();
        return $this->default;
    }

    /**
     * @inheritdoc
     */
    public function getSchema()
    {
        $this->init();
        return $this->schema;
    }

    /**
     * @inheritdoc
     */
    public function getValidator()
    {
        $this->init();
        return $this->Validator;
    }

    /**
     * @inheritdoc
     */
    public function getTable()
    {
        $this->init();
        return $this->table;
    }

    /**
     * @inheritdoc
     */
    public function toArray($deep=false)
    {
        $this->init();
        return get_object_vars($this);
    }

    /**
     * @inheritdoc
     */
    public function setDefault($default)
    {
        $this->init();
        $this->default = $default;
    }
    
    /**
     * @inheritdoc
     */
    public function setEncoding($encoding)
    {
        $this->init();
        $this->encoding = $encoding;
    }

    /**
     * @inheritdoc
     */
    public function setIsNullable($is_nullable)
    {
        $this->init();
        $this->is_nullable = $is_nullable;
    }

    /**
     * @inheritdoc
     */
    public function setIsPk($is_pk)
    {
        $this->init();
        $this->is_pk = $is_pk;
    }

    /**
     * @inheritdoc
     */
    public function setIsUnique($is_unique)
    {
        $this->init();
        $this->is_unique = $is_unique;
    }

    /**
     * @inheritdoc
     */
    public function setLength($length)
    {
        $this->init();
        $this->length = $length;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->init();
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function setPrecision($precision)
    {
        $this->init();
        $this->precision = $precision;
    }

    /**
     * @inheritdoc
     */
    public function setSchema($schema)
    {
        $this->init();
        $this->schema = $schema;
    }

    /**
     * @inheritdoc
     */
    public function setTable($table)
    {
        $this->init();
        $this->table = $table;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->init();
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function setValidator(Interfaces\Schema\Column\Validator $Validator)
    {
        $this->init();
        $this->Validator = $Validator;
    }

    /**
     * @inheritdoc
     */
    public function disableValidation()
    {
        $this->init();
        $this->Validator = null;
    }

    /**
     * @inheritdoc
     */
    public function hasValidator()
    {
        $this->init();
        return $this->Validator !== null;
    }

    /**
     * @inheritdoc
     */
    public function validateColumnValue($value)
    {
        try {
            $this->init();
            
            if ($this->hasValidator() === false) {
                return $value; //validation is disabled
            }

            if ($this->isNullable() && $value === null) {
                return $value; //no need to validate anything
            }

            $display_value = ($value === null) ? 'NULL' : $value;
            
            $validation_result = $this->getValidator()->validateValue($value);
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
        $this->init();
        
        if ($this->isNullable() && $value === null) {
            return $value;
        }

        if ($this->isPk() && $value === null) {
            return $value;
        }
        
        switch ($this->type) {
            case self::TYPE_STRING:
                if (trim($value) === '' && $this->isNullable()) {
                    $value = null;
                }
                return $value;
                break;
            
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

            case self::TYPE_JSON:
                return json_encode($value);
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
        $this->init();
        
        if ($this->isNullable() && $value === null) {
            return $value;
        }

        if ($this->isPk() && $value === null) {
            return $value;
        }
        
        switch ($this->type) {
            case self::TYPE_STRING:
                if (trim($value) === '' && $this->isNullable()) {
                    $value = null;
                }
                return $value;
                break;
            
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

            case self::TYPE_JSON:
                if (is_array($value)) {
                    return $value;
                }
                
                return json_decode($value, true);
                break;

            default:
                return $value;
                break;
        }
    }
}