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
    
    protected $offset = null;
    
    protected $limit = null;

    protected $order_by = null;
    
    protected $group_by = null;
    
    protected $sort = 'ASC';
    
    
    public function where(array $where)
    {
        $this->where = $this->arrayMergeDefault($this->where, $where);
        return $this;
    }
    
    public function offset($offset)
    {
        $this->offset = (int) $offset;
        return $this;
    }
    
    public function limit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }
    
    public function orderBy($order_by)
    {
        $this->order_by = $order_by;
        return $this;
    }
    
    public function groupBy($group_by)
    {
        $this->group_by = $group_by;
        return $this;
    }
    
    public function sortAsc()
    {
        $this->sort = 'ASC';
        return $this;
    }
    
    public function sortDesc()
    {
        $this->sort = 'DESC';
        return $this;
    }
    
    public function getWhereSql()
    {
        if (empty($this->where)) {
            return '';
        }
        
        $where_str = 'WHERE 1=1';
        foreach ($this->where as $field => $value) {
            $field_ok = str_replace('.', '_', $field); //replace z.id with z_id
            $where_str .= " AND ${field} = :${field_ok}";
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
        if ($this->order_by === null) {
            return '';
        }

        return 'ORDER BY '.$this->order_by.' '.$this->sort;
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
        
        return $where_str.'
            '.$order_by_str.'
            '.$offset_limit_sql.'
            '.$group_by;
    }

}