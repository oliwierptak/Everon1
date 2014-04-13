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
    public function getResult();

    /**
     * @param $result
     */
    public function setResult($result);

    /**
     * @param string $root
     * @return string
     */
    public function toJson($root = 'data');

    /**
     * @return null
     */
    public function getData();

    /**
     * @return string
     */
    public function toText();

    /**
     * @param mixed $data
     */
    public function setData($data);
}