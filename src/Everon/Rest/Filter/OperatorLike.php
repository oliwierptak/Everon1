<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Filter;



class OperatorLike extends Operator
{

    public function __construct($column, $value=null, $glue=null)
    {
        parent::__construct(\Everon\Rest\Filter::OPERATOR_TYPE_LIKE, $column, $value, $glue);
    }
}