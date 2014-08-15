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



class OperatorIsNot extends Operator
{

    /**
     * @var array
     */
    protected $allowed_value_types = ['string','integer','double','object','boolean','null'];

    public function __construct($column, $value=null, $column_glue=null, $glue=null)
    {
        parent::__construct(\Everon\Rest\Filter::OPERATOR_TYPE_IS_NOT, $column, $value, $column_glue, $glue);
    }
}