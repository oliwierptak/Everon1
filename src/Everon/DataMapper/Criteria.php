<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper;

use Everon\Helper;
use Everon\DataMapper\Exception;
use Everon\DataMapper\Interfaces;

class Criteria implements Interfaces\Criteria
{
    use Helper\Arrays;
    use Helper\ToArray;
    use Helper\ToString;
    
    protected $where = [];
    
    protected $where_or = [];

    protected $in = [];

    protected $ilike = [];

    protected $offset = null;

    protected $limit = null;

    protected $order_by = null;

    protected $group_by = null;

    protected $sort = 'ASC';


    protected $filter = [];
    

    /**
     * @inheritdoc
     */
    public function where(array $where)
    {
        $this->where = $this->arrayMergeDefault($this->where, $where);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function whereOr(array $where_or)
    {
        $this->where_or = $this->arrayMergeDefault($this->where_or, $where_or);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function in(array $in)
    {
        $this->in = $this->arrayMergeDefault($this->in, $in);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function ilike(array $ilike)
    {
        $this->ilike = $this->arrayMergeDefault($this->ilike, $ilike);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function offset($offset)
    {
        $this->offset = (int) $offset;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function limit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orderBy($order_by)
    {
        $this->order_by = $order_by;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function groupBy($group_by)
    {
        $this->group_by = $group_by;
        return $this;
    }
    
    public function sort($sort)
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function filter(array $filter)
    {
        $this->filter = array_merge($this->filter,$filter);
        return $this;
    }
    
    public function getWhereSql()
    {
        if (empty($this->where) && empty($this->where_or) && empty($this->in) && empty($this->ilike) && empty($this->filter)) {
            return '';
        }


        $where_str = '1=1';

        if (empty($this->filter) === false) {
            foreach($this->filter as $column => $data) {
                if (isset($data['query']) && (empty($data['query']) === false)) {
                    $operator = (isset($data['glue']) === true) ? $data['glue'] : 'AND';
                    $where_str .= ((empty($where_str) === true) ? ((isset($data['glue']) === true) ? $operator : '') : " {$operator} ") . "({$column} {$data['query']})";
                }


            }
        }

        foreach ($this->where as $field => $value) {
            $field_ok = str_replace('.', '_', $field); //replace z.id with z_id
            $where_str .= " AND ${field} = :${field_ok}";
        }
        $where_str = (empty($this->where) === false) ? '('.$where_str.')' : $where_str;

        $where_or_str = '';
        if (empty($this->where_or) === false) {
            $where_or_str = '';
            foreach ($this->where_or as $field => $value) {
                $field_ok = str_replace('.', '_', $field);
                $where_or_str .= " ${field} = :${field_ok} OR ";
            }
            $this->where = $this->arrayMergeDefault($this->where, $this->where_or);
            $where_or_str = substr($where_or_str, 0, strlen($where_or_str)-3);
            $where_or_str = ' OR ('.$where_or_str.')';
        }
        $where_str = 'WHERE '.$where_str.$where_or_str;
        
        if (empty($this->in) === false) {
            $where_str .= ' ';
            foreach ($this->in as $field => $values) {
                if (is_array($values) === false) {
                    continue;
                }
                $values = array_filter($values);
                $ilike_str = implode(',', $values);
                $where_str .= " AND ${field} IN (${ilike_str})";
            }
        }

        if (empty($this->ilike) === false) {
            $where_str .= ' ';
            foreach ($this->ilike as $field => $value) {
                $field_ok = str_replace('.', '_', $field); //replace z.id with z_id
                $where_str .= " AND ${field} ILIKE :${field_ok}";
            }
            $this->where = $this->arrayMergeDefault($this->where, $this->ilike);
        }

        return $where_str;
    }
    
    public function getOffsetLimitSql()
    {
        if ((int) $this->limit === 0 && $this->offset === null) {
            return '';
        }
        
        if ((int) $this->limit === 0 && $this->offset !== null) {
            return 'OFFSET '.$this->offset;
        }

        if ((int) $this->limit !== 0 && $this->offset === null) {
            return 'LIMIT '.$this->limit;
        }

        return 'LIMIT '.$this->limit. ' OFFSET '.$this->offset;
    }

    public function getOrderByAndSortSql()
    {
        if (empty($this->order_by)) {
            return '';
        }

        if (is_array($this->order_by)) {
            $order_by = implode(',', $this->order_by);
            
            if (is_array($this->sort)) {
                $order_by = '';
                foreach ($this->order_by as $order_field) {
                    $dir = isset($this->sort[$order_field]) ? $this->sort[$order_field] : 'ASC';
                    $order_by .= "${order_field} ".$dir.',';
                }
            }
        }
        else {
            $order_by = $this->order_by.' '.$this->sort;
        }
        
        $order_by = trim($order_by, ',');

        return 'ORDER BY '.$order_by;
    }
    
    public function getGroupBy()
    {
        if ($this->group_by === null) {
            return '';
        }

        return 'GROUP BY '.$this->group_by;
    }
    
    protected function getToArray()
    {
        return [
            'where' => $this->where,
            'order_by' => $this->order_by,
            'sort' => $this->sort,
            'offset' => $this->offset,
            'limit' => $this->limit,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @inheritdoc
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @inheritdoc
     */
    public function getOrderBy()
    {
        return $this->order_by;
    }

    /**
     * @inheritdoc
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @inheritdoc
     */
    public function getWhere()
    {

        if (empty($this->filter) !== true) {
            foreach($this->filter as $column => $data) {
                $this->where = $this->arrayMergeDefault($this->where, $data['values']);
            }
        }


        $where = [];
        foreach ($this->where as $field => $value) {
            $field_ok = str_replace('.', '_', $field); //replace z.id with z_id
            $where[$field_ok] = $value;
        }

        return $where;
    }
    
    protected function getToString()
    {
        $where_str = $this->getWhereSql();
        $order_by_str = $this->getOrderByAndSortSql();
        $offset_limit_sql = $this->getOffsetLimitSql();
        $group_by = $this->getGroupBy();
        
        return "$where_str
            $group_by
            $order_by_str
            $offset_limit_sql
            ";
    }

}