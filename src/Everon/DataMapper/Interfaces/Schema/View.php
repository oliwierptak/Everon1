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

interface View extends Table
{
    /**
     * @todo remove this
     * @param string $original_name
     */
    function setOriginalName($original_name);

    /**
     * @todo remove this
     * @return string
     */
    function getOriginalName();

}
