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

class NotLike extends \Everon\DataMapper\Criteria\Operator\Like implements Interfaces\Criteria\Operator
{
    protected $type = self::TYPE_NOT_LIKE;
    
    public function getTypeAsSql()
    {
        return self::SQL_NOT_LIKE;
    }
}