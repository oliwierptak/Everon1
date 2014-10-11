<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Criteria\Operator;

use Everon\DataMapper\Interfaces;

class NotBetween extends \Everon\DataMapper\Criteria\Operator\Between
{
    protected $type = self::TYPE_NOT_BETWEEN;

    public function getTypeAsSql()
    {
        return self::SQL_NOT_BETWEEN;
    }
}