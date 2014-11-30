<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces\Schema\Column;

interface Validator extends \Everon\Interfaces\Dependency\Factory 
{
    /**
     * @param mixed $value
     * @return bool
     */
    function validateValue($value);
}