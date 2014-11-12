<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Interfaces;

use Everon\DataMapper;
use Everon\Rest;

interface Filter
{
    /**
     * @param array $filter
     * @return DataMapper\Interfaces\Criteria\Builder|void
     * @throws Rest\Exception\Filter
     */
    function toCriteria(array $filter);
}