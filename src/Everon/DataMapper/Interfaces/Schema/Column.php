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

interface Column extends Arrayable, \Everon\Interfaces\Dependency\Factory
{
    /**
     * @return boolean
     */
    function isPk();

    function markAsPk();

    function unMarkAsPk();

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
     * @return Column\Validator
     */
    function getValidator();

    /**
     * @param $value
     * @return mixed
     * @throws \Everon\DataMapper\Exception\Column
     */
    function validateColumnValue($value);

    /**
     * Converts all values so they can be consumed in a sql query, 
     * eg. DateTime into its text representation in proper format.
     * 
     * @param $value
     * @return string
     */
    function getDataForSql($value);

    /**
     * Converts values fro SQL into PHP, eg. timestamp into DateTime, 't' into true, etc...
     * 
     * @param $value
     * @return mixed
     */
    function getColumnDataForEntity($value);

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
     * @param Column\Validator $Validator
     */
    function setValidator(Column\Validator $Validator);

    function disableValidation();

    /**
     * @return bool
     */
    function hasValidator();
}