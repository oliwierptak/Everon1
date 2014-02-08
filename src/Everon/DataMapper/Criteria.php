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
    
    protected $where = [];
    
    protected $offset = null;
    
    protected $limit = null;
    
    protected $order_by = null;
    
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
        
        $where_str = '1=1';
        foreach ($this->where as $field => $value) {
            $where_str .= " AND ${field} = :${field}";
        }
        return [$where_str, $this->where];
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

    public function getOrderBySortSql()
    {
        if ($this->order_by === null) {
            return '';
        }

        return 'ORDER BY '.$this->order_by.' '.$this->sort;
    }
    
    public function getToArray()
    {
        return [
            'where' => $this->where,
            'order_by' => $this->order_by,
            'sort' => $this->sort,
            'offset' => $this->offset,
            'limit' => $this->limit,
        ];
    }
}