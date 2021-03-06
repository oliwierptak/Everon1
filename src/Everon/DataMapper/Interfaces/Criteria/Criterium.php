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

interface Criterium extends \Everon\Interfaces\Dependency\Factory, \Everon\Interfaces\Arrayable
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
    function getOperatorType();

    /**
     * @param string
     */
    function setOperatorType($operator);

    /**
     * @return string
     */
    function getValue();

    /**
     * @param string $value
     */
    function setValue($value);

    /**
     * @return string
     */
    function getGlue();

    function glueByAnd();

    function glueByOr();

    function resetGlue();

    /**
     * @return mixed
     */
    function getPlaceholder();

    /**
     * @param mixed $placeholder
     */
    function setPlaceholder($placeholder);

    /**
     * @return string
     */
    function getPlaceholderAsParameter();

    /**
     * @return \Everon\DataMapper\Interfaces\SqlPart
     */
    function getSqlPart();

    /**
     * @param \Everon\DataMapper\Interfaces\SqlPart $SqlPart
     */
    function setSqlPart(\Everon\DataMapper\Interfaces\SqlPart $SqlPart);
}