<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces\Schema;

use Everon\Interfaces\Arrayable;
use Everon\Interfaces\Immutable;

interface Column extends Arrayable
{
    /**
     * @return boolean
     */
    function isPk();

    /**
     * @return string
     */
    function getName();

    /**
     * @return string
     */
    function getType();

    /**
     * @return int
     */
    function getLength();
    
    /**
     * @return boolean
     */
    function isNullable();

    /**
     * @return mixed
     */
    function getDefault();

    /**
     * @return string
     */
    function getSchema();
    
    /**
     * @return array
     */
    function getValidationRules();

    /**
     * @param $value
     * @return mixed
     * @throws \Everon\DataMapper\Exception\Column
     */
    function validateColumnValue($value);

    /**
     * @param $value
     * @return string
     */
    function getDataValue($value);

    /**
     * @return string
     */
    function getTable();

    /**
     * @param mixed $default
     */
    function setDefault($default);

    /**
     * @param string $encoding
     */
    function setEncoding($encoding);

    /**
     * @param boolean $is_nullable
     */
    function setIsNullable($is_nullable);

    /**
     * @param boolean $is_pk
     */
    function setIsPk($is_pk);

    /**
     * @param boolean $is_unique
     */
    function setIsUnique($is_unique);

    /**
     * @param int $length
     */
    function setLength($length);

    /**
     * @param string $name
     */
    function setName($name);

    /**
     * @param int $precision
     */
    function setPrecision($precision);

    /**
     * @param string $schema
     */
    function setSchema($schema);

    /**
     * @param string $table
     */
    function setTable($table);

    /**
     * @param string $type
     */
    function setType($type);

    /**
     * @param string $validation_rules
     */
    function setValidationRules($validation_rules);
}
