<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces\Criteria;

interface Criterium extends \Everon\Interfaces\Stringable
{
    /**
     * @return string
     */
    function getColumn();

    /**
     * @param string $column
     */
    function setColumn($column);

    /**
     * @return string
     */
    function getOperator();

    /**
     * @param string $operator
     */
    function setOperator($operator);

    /**
     * @return string
     */
    function getValue();

    /**
     * @param string $value
     */
    function setValue($value);

    /**
     * @return mixed
     */
    function getPlaceholder();

    /**
     * @param mixed $placeholder
     */
    function setPlaceholder($placeholder);
}