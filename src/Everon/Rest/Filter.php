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
                list($class_name,$column,$value,$glue) = $this->resolveClassNameColumnValueAndGlueFromItem($item);

                $Operator = $this->getFactory()->buildRestFilterOperator($class_name, $column,$value,$glue);
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
        $list = $this->castFilterCollectionIntoArray();
        if (empty($list) !== true) {
            foreach($list as $column => $value) {
                $Criteria->where([$column=>$value]);
            }
        }
    }

    /**
     * @return \Everon\Interfaces\Collection
     */
    protected function castFilterCollectionIntoArray()
    {
        $list = [];
        /**
         * @var \Everon\Rest\Interfaces\FilterOperator $FilterOperator
         */
        foreach($this->getFilterCollection() as $FilterOperator) {
            list($operator, $column, $value, $glue) = array_values($FilterOperator->toArray());
            $columnPrefix  = $operator;
            $valueType = gettype($value);

            if (isset($list[':'.$column]) == true) {
                $columnPrefix = $list[':'.$column] . ' '.$glue.' ' . $columnPrefix;
            }

            if ($valueType === 'array') {
                foreach($value as $k => &$v) {
                    if ($v instanceof \DateTime) {
                        //$v = $v->format('Y-m-d H:i:s');
                        $v = $this->dateAsPostgreSql($v);
                    }
                }
                if (in_array($operator, [self::OPERATOR_TYPE_BETWEEN, self::OPERATOR_TYPE_NOT_BETWEEN])) {
                    $value = implode(' AND ',$value);
                } 
                else {
                    $value = "('".implode("','",$value)."')";
                }
            } 
            else {
                if ($value instanceof \DateTime) {
                    $value = $this->dateAsPostgreSql($value);
                    //$value = $value->format();
                }
            }
            $list[$column] = $columnPrefix . ' '.$value;
        }
        return new \Everon\Helper\Collection($list);
    }

    /**
     * @param array $item
     * @return array
     */
    protected function resolveClassNameColumnValueAndGlueFromItem(array $item=[])
    {
        $class_name = $this->getClassNameByType($item['operator']);
        $column = (isset($item['column']) ? $item['column'] : null);
        $value = (isset($item['value']) ? $item['value'] : null);
        $glue = (isset($item['glue']) ? $item['glue'] : null);
        return [$class_name,$column,$value,$glue];
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
                    if (($columnOperators[$columnName] === $FilterOperator->getOperator()) && ($FilterOperator->getGlue() === null)) {
                        throw new Exception\Filter('Duplicate operator "%s"  on column "%s" without a valid glue property', [$FilterOperator->getOperator(), $columnName]);
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
