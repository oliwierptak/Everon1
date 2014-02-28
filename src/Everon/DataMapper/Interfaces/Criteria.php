<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces;

interface Criteria extends \Everon\Interfaces\Arrayable
{
    public function offset($offset);

    public function getOrderByAndSortSql();

    public function orderBy($order_by);

    public function getOffsetLimitSql();

    public function getWhereSql();

    public function sortDesc();

    public function where(array $where);

    public function sortAsc();

    public function limit($limit);
}
