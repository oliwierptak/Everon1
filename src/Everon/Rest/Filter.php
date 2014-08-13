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


use Everon\DataMapper\Interfaces\Criteria;
use Everon\Rest\Exception;


class Filter implements Interfaces\Filter
{
    use \Everon\Dependency\Injection\Factory;
    use \Everon\Helper\DateFormatter;


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
     * @var \Everon\Helper\Collection
     */
    protected $FilterColumnArrayList = null;


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
     * @return array
     */
    public function convertToCriteriaCollection()
    {
        return $this->castFilterCollectionIntoArray();
    }


    /**
     * @param Criteria $Criteria
     * @return mixed|void
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
     * @return array
     */
    protected function castFilterCollectionIntoArray()
    {
        if ($this->FilterColumnArrayList === null) {
            $list = [];
            foreach($this->getFilterCollection() as $filter) {
                list($operator,$column,$value,$glue) = [$filter->getOperator(),$filter->getColumn(),$filter->getValue(),$filter->getGlue()];
                $columnPrefix  = $operator;
                $valueType = gettype($value);

                if (isset($list[':'.$column]) == true) {
                    $columnPrefix = $list[':'.$column] . ' '.$glue.' ' . $columnPrefix;
                }

                if ($valueType == 'array') {
                    foreach($value as $k => &$v) {
                        if ($v instanceof \DateTime) {
                            $v = $v->format('Y-m-d H:i:s');     //@todo replace with proper datetime format function (timezone, etc)
                        }
                    }
                    if (in_array($operator,['BETWEEN','NOT BETWEEN'])) {
                        $value = implode(' AND ',$value);
                    } else {
                        $value = "('".implode("','",$value)."')";
                    }
                } else {
                    if ($value instanceof \DateTime) {
                        $value = $value->format('Y-m-d H:i:s'); //@todo replace with proper datetime format function (timezone, etc)
                    }
                }
                $list[':'.$column] = $columnPrefix . ' '.$value;
            }
            $this->FilterColumnArrayList = new \Everon\Helper\Collection($list);
        }
        return $this->FilterColumnArrayList;
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
    protected function assertColumnCombinations(array &$data=[])
    {
        $columnOperators = [];
        if (empty($data) !== true) {
            foreach($data as $filter) {
                $columnName = $filter->getColumn();
                if (isset($columnOperators[$columnName]) != true) {
                    $columnOperators[$columnName] = $filter->getOperator();
                } else {
                    if (($columnOperators[$columnName] == $filter->getOperator()) && ($filter->getGlue() == null)) {
                        throw new Exception\Filter('Duplicate operator "%s"  on column "%s" without a valid glue property',$filter->getOperator(),$columnName);
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
        $this->assertArrayKeyValues($item,['column','operator','value']);
        $this->assertColumnName($item['column']);
    }

    /**
     * @param array $array
     * @param array $keyvalues
     * @throws \Exception
     */
    protected function assertArrayKeyValues(array $array=[],array $keyvalues=[])
    {
        $missing = array_diff($keyvalues,array_keys($array));
        if (empty($missing) != true) {
            throw new Exception\Filter('Missing the following key value(s): "%s"',implode("','",$missing));
        }
    }

    /**
     * @param $columnName
     * @todo implementation
     */
    protected  function assertColumnName($columnName) {}
}
