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



class OperatorIn extends Operator
{
    /**
     * @var int
     */
    protected $min_value_count = 0;

    /**
     * @var int
     */
    protected $max_value_count = 0;
    /**
     * @var array
     */
    protected $allowed_value_types = ['array'];

    public function __construct($column, $value=null, $column_glue=null, $glue=null)
    {
        parent::__construct(\Everon\Rest\Filter::OPERATOR_TYPE_IN, $column, $value, $column_glue, $glue);
    }
}