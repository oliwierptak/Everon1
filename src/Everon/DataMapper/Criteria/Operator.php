<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Criteria;

use Everon\DataMapper\Interfaces;
use Everon\Helper;
use Everon\Rest\Exception;

abstract class Operator implements Interfaces\Criteria\Operator
{
    const TYPE_EQUAL = '=';
    const TYPE_LIKE = 'LIKE';
    const TYPE_IN = 'IN';
    
    protected $type = null;
    
    /*
    const TYPE_BETWEEN = 'BETWEEN';
    const TYPE_SMALLER_THAN = '<';
    const TYPE_GREATER_THAN = '>';
    const TYPE_GREATER_OR_EQUAL = '> = ';
    const TYPE_SMALLER_OR_EQUAL = '< = ';
    const TYPE_NOT_EQUAL = '! = ';
    const TYPE_IS = 'IS';
    const TYPE_IS_NOT = 'ISNOT';
    const TYPE_IN = 'IN';
    const TYPE_NOT_IN = 'NOTIN';
    const TYPE_NOT_BETWEEN = 'NOTBETWEEN';
    */

    /**
     * @inheritdoc
     */
    public function toSql(Interfaces\Criteria\Criterium $Criterium)
    {
        return sprintf("%s %s %s", $Criterium->getColumn(), $Criterium->getOperator(), $Criterium->getPlaceholder());
    }
}