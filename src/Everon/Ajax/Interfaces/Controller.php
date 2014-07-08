<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Ajax\Interfaces;

interface Controller extends \Everon\Interfaces\Controller
{
    /**
     * @param array $json_data
     */
    function setJsonData($json_data);

    /**
     * @return array
     */
    function getJsonData();

}