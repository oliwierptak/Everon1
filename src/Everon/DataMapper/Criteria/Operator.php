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
    const TYPE_EQUAL = 'Equal';
    const TYPE_NOT_EQUAL = 'NotEqual';
    const TYPE_LIKE = 'Like';
    const TYPE_NOT_LIKE = 'Like';
    const TYPE_ILIKE = 'Ilike'; //case insensitive like
    const TYPE_NOT_ILIKE = 'NotIlike';
    const TYPE_IN = 'In';
    const TYPE_NOT_IN = 'NotIn';
    const TYPE_IS = 'Is';
    const TYPE_NOT_IS = 'NotIs';
    const TYPE_GREATER_THEN = 'GreaterThen';
    const TYPE_GREATER_OR_EQUAL = 'GreaterOrEqual';
    const TYPE_SMALLER_THEN = 'SmallerThen';
    const TYPE_SMALLER_OR_EQUAL = 'SmallerOrEqual';
    const TYPE_BETWEEN = 'Between';
    const TYPE_NOT_BETWEEN = 'NotBetween';
    const TYPE_RAW = 'Raw';

    const SQL_EQUAL = '=';
    const SQL_NOT_EQUAL = '!=';
    const SQL_LIKE = 'LIKE';
    const SQL_NOT_LIKE = 'NOT LIKE';
    const SQL_ILIKE = 'ILIKE'; //case insensitive like
    const SQL_NOT_ILIKE = 'NOT ILIKE';
    const SQL_IN = 'IN';
    const SQL_NOT_IN = 'NOT IN';
    const SQL_IS = 'IS';
    const SQL_NOT_IS = 'IS NOT';
    const SQL_GREATER_THEN = '>';
    const SQL_GREATER_OR_EQUAL = '>=';
    const SQL_SMALLER_THEN = '<';
    const SQL_SMALLER_OR_EQUAL = '<=';
    const SQL_BETWEEN = 'BETWEEN';
    const SQL_NOT_BETWEEN = 'NOT BETWEEN';
    const SQL_RAW = 'RAW';

    /**
     * @var string
     */
    protected $type = null;
    
    /*
    const TYPE_BETWEEN = 'BETWEEN';
    const TYPE_NOT_BETWEEN = 'NOTBETWEEN';
    */

    /**
     * @inheritdoc
     */
    abstract public function getTypeAsSql();

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function toSqlPartData(Interfaces\Criteria\Criterium $Criterium)
    {
        $sql = sprintf("%s %s %s", $Criterium->getColumn(), $this->getTypeAsSql(), $Criterium->getPlaceholder());
        $params = [
            $Criterium->getPlaceholderAsParameter() => $Criterium->getValue() 
        ];
        
        if ($Criterium->getValue() === null) {
            $params = [];
        }
        
        return [$sql, $params];
    }
    
}