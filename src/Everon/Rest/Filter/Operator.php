<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Filter;

use Everon\Helper;
use Everon\Rest\Interfaces\FilterOperator;
use Everon\Rest\Exception;

abstract class Operator implements FilterOperator
{
    use Helper\ToArray;
    
    /**
     * @var string
     */
    protected $column = null;

    /**
     * @var string
     */
    protected $value = null;

    /**
     * @var string
     */
    protected $operator = null;

    /**
     * @var string
     */
    protected $column_glue = null;

    /**
     * @var string
     */
    protected $glue = null;


    /**
     * @var int
     */
    protected $min_value_count = 1;

    /**
     * @var int
     */
    protected $max_value_count = 1;


    /**
     * @var array
     */
    protected $allowed_value_types = ['string','integer','double','object','boolean'];


    const GLUE_TYPE_AND = 'AND';
    const GLUE_TYPE_OR = 'OR';
    const GLUE_TYPE_NULL = null;


    public function __construct($operator, $column, $value=null, $column_glue=null, $glue=null)
    {
        $this->operator = $operator;
        $this->column = $column;
        $this->value = ($value === null) ? 'NULL' : $value;
        $this->column_glue = $column_glue;
        $this->glue = $glue;

        $this->assertValue();
        $this->assertColumnGlue();
        $this->assertGlue();
    }

    /**
     * @param string $column
     */
    public function setColumn($column)
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param string $glue
     */
    public function setGlue($glue)
    {
        $this->glue = $glue;
    }

    /**
     * @return string
     */
    public function getGlue()
    {
        return $this->glue;
    }

    /**
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $column_glue
     */
    public function setColumnGlue($column_glue)
    {
        $this->column_glue = $column_glue;
    }

    /**
     * @return string
     */
    public function getColumnGlue()
    {
        return $this->column_glue;
    }

    /**
     * @brief assertion of value
     */
    protected function assertValue()
    {
        $this->assertValueType();
        $this->assertValueCount();
    }

    /**
     * assertion of $glue
     * @throws \Everon\Rest\Exception\Filter
     */
    protected function assertGlue()
    {
        if (($this->glue !== self::GLUE_TYPE_AND) && ($this->glue !== self::GLUE_TYPE_OR) && ($this->glue !== self::GLUE_TYPE_NULL)) {
            throw new Exception\Filter('"%s" is not allowed for as a glue on "%s"',[$this->glue,$this->column]);
        }
    }

    /**
     * assertion of $column_glue
     * @throws \Everon\Rest\Exception\Filter
     */
    protected function assertColumnGlue()
    {
        if (($this->column_glue !== self::GLUE_TYPE_AND) && ($this->column_glue !== self::GLUE_TYPE_OR) && ($this->column_glue !== self::GLUE_TYPE_NULL)) {
            throw new Exception\Filter('"%s" is not allowed for as a column_glue on "%s"',[$this->glue,$this->column]);
        }
    }
    /**
     * @throws \Everon\Rest\Exception\Filter
     */
    protected function assertValueType()
    {
        $valueType = gettype($this->value);
        if (in_array(strtolower($valueType),$this->allowed_value_types) != true) {
            throw new Exception\Filter('"%s" is not allowed for operator "%s"',[$valueType,$this->operator]);
        }
    }

    /**
     * @throws \Everon\Rest\Exception\Filter
     */
    protected function assertValueCount()
    {
        if ($this->min_value_count != 0 || $this->max_value_count != 0) {
            $valueCount = ($this->value === null) ? 1 : (int) count($this->value);
            if ($this->min_value_count > $valueCount || $this->max_value_count < $valueCount) {
                throw new Exception\Filter('"%s" number of values is incorrect, add a minimum of "%s" and a maximum of "%s"!',[$valueCount,$this->min_value_count,$this->max_value_count]);
            }
        }
    }
    
    protected function getToArray()
    {
        return [
            'operator' => $this->getOperator(),
            'column' => $this->getColumn(),
            'value' => $this->getValue(),
            'column_glue' => $this->getColumnGlue(),
            'glue' => $this->getGlue()  
        ];
    }

}