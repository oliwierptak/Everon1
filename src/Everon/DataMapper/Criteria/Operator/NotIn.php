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

class NotIn extends \Everon\DataMapper\Criteria\Operator\In
{
    protected $type = self::TYPE_NOT_IN;

    public function getTypeAsSql()
    {
        return self::SQL_NOT_IN;
    }
}