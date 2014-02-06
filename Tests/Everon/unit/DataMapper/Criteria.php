<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\DataMapper;

use Everon\DataMapper\Criteria;
use Everon\Interfaces;
use Everon\Helper;

class CriteriaTest extends \Everon\TestCase
{
    use Helper\Arrays;
    
    function testConstructor()
    {
        $Criteria = new \Everon\DataMapper\Criteria();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Criteria', $Criteria);
    }

    /**
     * @dataProvider dataProvider
     */
    function testWhere(Criteria $Criteria)
    {
        $Criteria->where(['first_name' => 'John']);
        $Criteria->where(['last_name' => 'Doe']);
        
        $where = $Criteria->toArray()['where'];
        
        $this->assertInternalType('array', $where);
        $this->assertEquals($where['first_name'], 'John');
        $this->assertEquals($where['last_name'], 'Doe');
    }
    
    /**
     * @dataProvider dataProvider
     */ 
    function testGetWhereToSql(Criteria $Criteria)
    {
        $where = $Criteria->getWhereSql();
        $this->assertEquals('', $where);

        $Criteria->where(['first_name' => 'John']);
        $Criteria->where(['last_name' => 'Doe']);
        $where = $Criteria->getWhereSql();
        $this->assertInternalType('array', $where);
        
        list($where_sql, $params) = $where;
        $this->assertEquals('1=1 AND last_name => :last_name AND first_name => :first_name', $where_sql);
        $this->assertInternalType('array', $params);
        $this->assertEquals($params['first_name'], 'John');
        $this->assertEquals($params['last_name'], 'Doe');
    }

    /**
     * @dataProvider dataProvider
     */
    function testGetOffsetLimitSql(Criteria $Criteria)
    {
        $offset_limit_sql = $Criteria->getOffsetLimitSql();
        $this->assertEquals('', $offset_limit_sql);

        $Criteria->limit(10);
        $offset_limit_sql = $Criteria->getOffsetLimitSql();
        $this->assertEquals('LIMIT 10', $offset_limit_sql);

        $Criteria->offset(15)->limit(0);
        $Criteria->limit(0)->offset(15);
        $offset_limit_sql = $Criteria->getOffsetLimitSql();
        $this->assertEquals('OFFSET 15', $offset_limit_sql);
        
        $Criteria->offset(10)->limit(20);
        $offset_limit_sql = $Criteria->getOffsetLimitSql();
        $this->assertEquals('LIMIT 20 OFFSET 10', $offset_limit_sql);
    }

    /**
     * @dataProvider dataProvider
     */
    function testGetOrderBySortSql(Criteria $Criteria)
    {
        $order_by_sort_sql = $Criteria->getOrderBySortSql();
        $this->assertEquals('', $order_by_sort_sql);

        $Criteria->orderBy('login');
        $order_by_sort_sql = $Criteria->getOrderBySortSql();
        $this->assertEquals('ORDER BY login ASC', $order_by_sort_sql);

        $Criteria->orderBy('id')->sortDesc();
        $order_by_sort_sql = $Criteria->getOrderBySortSql();
        $this->assertEquals('ORDER BY id DESC', $order_by_sort_sql);
    }

    function dataProvider()
    {
        $filter = [
            'where' => [],
            'limit' => 10,
            'offset' => 0,
        ];

        $Criteria = new \Everon\DataMapper\Criteria();
        
        return [
            [$Criteria, $filter]
        ];
    }

}
