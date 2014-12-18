<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

interface Response
{
    /**
     * @return bool
     */
    function getResult();

    /**
     * @param $result
     */
    function setResult($result);

    /**
     * @return string
     */
    function toJson();

    /**
     * @return null
     */
    function getData();

    /**
     * @return string
     */
    function toText();

    /**
     * @param mixed $data
     */
    function setData($data);
}