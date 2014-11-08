<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config\Interfaces;


interface LoaderItem extends \Everon\Interfaces\Arrayable
{
    function getFilename();

    /**
     * @return array
     */
    function getData();

    /**
     * @param $data
     */
    function setData(array $data);
    
    /**
     * @return boolean
     */
    function isCachingEnabled();

    /**
     * @param boolean $use_cache
     */
    function setIsCachingEnabled($use_cache);
}
