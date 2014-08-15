<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest;

use Everon\Helper;
use Everon\DataMapper\Interfaces\Criteria;
use Everon\Rest\Exception;


class Filter implements Interfaces\Filter
{
    use Helper\DateFormatter;
    use \Everon\Dependency\Injection\Factory;
    use Helper\AlphaId;
    use Helper\Arrays;


    const OPERATOR_TYPE_LIKE                    = 'LIKE';
    const OPERATOR_TYPE_EQUAL                   = '=';
    const OPERATOR_TYPE_BETWEEN                 = 'BETWEEN';
    const OPERATOR_TYPE_SMALLER_THAN            = '<';
    const OPERATOR_TYPE_GREATER_THAN            = '>';
    const OPERATOR_TYPE_GREATER_OR_EQUAL        = '>=';
    const OPERATOR_TYPE_SMALLER_OR_EQUAL        = '<=';
    const OPERATOR_TYPE_NOT_EQUAL               = '!=';
    const OPERATOR_TYPE_IS                      = 'IS';
    const OPERATOR_TYPE_IS_NOT                  = 'IS NOT';
    const OPERATOR_TYPE_IN                      = 'IN';
    const OPERATOR_TYPE_NOT_IN                  = 'NOT IN';
    const OPERATOR_TYPE_NOT_BETWEEN             = 'NOT BETWEEN';


    protected $mappers = [
        self::OPERATOR_TYPE_LIKE                =>  'OperatorLike',
        self::OPERATOR_TYPE_EQUAL               =>  'OperatorEqual',
        self::OPERATOR_TYPE_BETWEEN             =>  'OperatorBetween',
        self::OPERATOR_TYPE_NOT_BETWEEN         =>  'OperatorNotBetween',
        self::OPERATOR_TYPE_SMALLER_THAN        =>  'OperatorSmallerThan',
        self::OPERATOR_TYPE_GREATER_THAN        =>  'OperatorGreaterThan',
        self::OPERATOR_TYPE_GREATER_OR_EQUAL    =>  'OperatorGreaterOrEqual',
        self::OPERATOR_TYPE_SMALLER_OR_EQUAL    =>  'OperatorSmallerOrEqual',
        self::OPERATOR_TYPE_NOT_EQUAL           =>  'OperatorNotEqual',
        self::OPERATOR_TYPE_IS                  =>  'OperatorIs',
        self::OPERATOR_TYPE_IS_NOT              =>  'OperatorIsNot',
        self::OPERATOR_TYPE_IN                  =>  'OperatorIn',
        self::OPERATOR_TYPE_NOT_IN              =>  'OperatorNotIn'
    ];


    /**
     * @var \Everon\Helper\Collection
     */
    protected $FilterDefinition = null;

    /**
     * @var \Everon\Helper\Collection
     */
    protected $FilterCollection = null;


    /**
     * @param \Everon\Helper\Collection $Collection
     */
    public function __construct(\Everon\Helper\Collection $Collection)
    {
        $this->FilterDefinition = $Collection;
        $this->FilterCollection = new \Everon\Helper\Collection([]);
    }

    /**
     * @param \Everon\Helper\Collection $Collection
     */
    public function setFilterCollection(\Everon\Helper\Collection $Collection)
    {
        $this->FilterCollection = $Collection;
    }

    /**
     * @return \Everon\Helper\Collection
     */
    public function getFilterCollection()
    {
        $data = [];
        if ($this->FilterCollection->isEmpty()) {
            foreach ($this->FilterDefinition as $item) {
                $this->assertFilterItem($item);
                list($class_name,$column,$value,$column_glue,$glue) = $this->resolveClassNameColumnValueAndGlueFromItem($item);
                $Operator = $this->getFactory()->buildRestFilterOperator($class_name, $column,$value,$column_glue,$glue);
                $data[] = $Operator;
            }
            $this->assertColumnCombinations($data);
            $this->FilterCollection = new \Everon\Helper\Collection($data);
        }

        return $this->FilterCollection;
    }

    /**
     * @param \Everon\Helper\Collection $Collection
     */
    public function setFilterDefinition(\Everon\Helper\Collection $Collection)
    {
        $this->FilterCollection = new \Everon\Helper\Collection([]);
        $this->FilterDefinition = $Collection;
    }

    /**
     * @return \Everon\Helper\Collection
     */
    public function getFilterDefinition()
    {
        return $this->FilterDefinition;
    }

    protected function getClassNameByType($type)
    {
        if (isset($this->mappers[$type]) === false) {
            throw new Exception\Filter('Invalid operator type: "%s"', $type);
        }

        return $this->mappers[$type];
    }

    /**
     * @param Criteria $Criteria
     * @return void
     */
    public function assignToCriteria(Criteria $Criteria)
    {
        $list = $this->castFilterCollectionIntoColumnGroupCollection();
        if (empty($list) !== true) {
            foreach($list as $column => $value) {
                $Criteria->filter([$column=>$value]);
            }
        }
    }

    /**
     * @return \Everon\Interfaces\Collection
     */
    protected function castFilterCollectionIntoColumnGroupCollection()
    {
        $col = [];
        $keyBindValues = [];

        /**
         * @var \Everon\Rest\Interfaces\FilterOperator $FilterOperator
         */
        foreach($this->getFilterCollection() as $index => $FilterOperator) {

            $operatorAssigns = [];
            list($operator, $column, $value, $column_glue, $glue) = array_values($FilterOperator->toArray());
            $values = [];

            $queryString = '(';

            $valueType = gettype($value);

            if ($valueType === 'array') {
                foreach($value as $k => &$v) {
                    if ($v instanceof \DateTime) {
                        $v = $this->dateAsPostgreSql($v);
                    }
                }

                if (in_array($operator, [self::OPERATOR_TYPE_BETWEEN, self::OPERATOR_TYPE_NOT_BETWEEN])) {
                    $queryString .= $this->createUniqueAssignBindKey($keyBindValues,$operatorAssigns) . ' AND ' . $this->createUniqueAssignBindKey($keyBindValues,$operatorAssigns);
                }
                else {
                    $queryString .= '(';
                    foreach($value as $k) {
                        $queryString .= $this->createUniqueAssignBindKey($keyBindValues,$operatorAssigns).',';

                    }
                    $queryString  = (substr($queryString,-1,1) == ',') ? substr($queryString,0,-1) : $queryString;
                    $queryString .= ')';
                }
                $values = array_merge($values,$value);
            } 
            else {
                $queryString .= $this->createUniqueAssignBindKey($keyBindValues,$operatorAssigns);
                if ($value instanceof \DateTime) {
                    $values = array_merge($values,[$this->dateAsPostgreSql($value)]);
                } else {
                    $values = array_merge($values,[$value]);
                }
            }
            $values = $this->arrayReplaceKeysByArrayValues($values,$operatorAssigns);

            $col[$column]['query']  = (isset($col[$column]['query']) === true)  ?   ($col[$column]['query']." {$column_glue} {$column} {$operator} {$queryString})") : "{$operator} {$queryString})";
            $col[$column]['values'] = (isset($col[$column]['values']) === true) ?   array_merge($col[$column]['values'], $values) : $values;
            $col[$column]['glue']   = ((isset($col[$column]['glue']) === true) && ($glue === null)) ?  $col[$column]['glue']  : $glue;
        }

       return  new \Everon\Helper\Collection($col);
    }


    /**
     * @param array $assign
     * @param array $iteratorContainer
     * @return string
     */
    protected function createUniqueAssignBindKey(array &$assign, array &$iteratorContainer = [])
    {
        $key = ':'.$this->calculateRandomAssignKey();
        while (in_array($key,$assign)) {
            $key = ':'.$this->calculateRandomAssignKey();
        }
        $assign[] = $key;
        $iteratorContainer[] = $key;
        return $key;
    }

    /**
     * @todo move to different class, perhaps?
     */
    protected function calculateRandomAssignKey($length=6)
    {
        $length = abs((int) $length);
        // Available chars
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // Make the code
        $code = '';
        for ($i=0; $i<$length; $i++) {
            $code .= substr($chars, rand(0, strlen($chars)), 1);
        }
        return $code;

    }

    /**
     * @param array $item
     * @return array
     */
    protected function resolveClassNameColumnValueAndGlueFromItem(array $item=[])
    {
        $class_name     = $this->getClassNameByType($item['operator']);
        $column         = (isset($item['column']) ? $item['column'] : null);
        $value          = (isset($item['value']) ? $item['value'] : null);
        $column_glue    = (isset($item['column_glue']) ? $item['column_glue'] : null);
        $glue           = (isset($item['glue']) ? $item['glue'] : null);
        return [$class_name, $column, $value,$column_glue,  $glue];
    }

    /**
     * @param array $data
     * @throws Exception\Filter
     */
    protected function assertColumnCombinations(array $data)
    {
        $columnOperators = [];
        if (empty($data) !== true) {
            /**
             * @var \Everon\Rest\Interfaces\FilterOperator $FilterOperator
             */
            foreach($data as $FilterOperator) {
                $columnName = $FilterOperator->getColumn();
                if (isset($columnOperators[$columnName]) !== true) {
                    $columnOperators[$columnName] = $FilterOperator->getOperator();
                }
                else {
                    if ($FilterOperator->getColumnGlue() === null) {
                        throw new Exception\Filter('Duplicate operator  on column "%s" without a valid "column_glue" property', $columnName);
                    }
                }
            }
        }
    }

    /**
     * @param array $item
     */
    protected function assertFilterItem(array $item=[])
    {
        $this->assertArrayKeyValues($item, ['column', 'operator', 'value']);
    }

    /**
     * @param array $array
     * @param array $key_values
     * @throws \Exception
     */
    protected function assertArrayKeyValues(array $array, array $key_values)
    {
        $missing = array_diff($key_values,array_keys($array));
        if (empty($missing) !== true) {
            throw new Exception\Filter('Missing the following key value(s): "%s"', implode("','", $missing));
        }
    }
}
