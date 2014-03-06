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

use Everon\Interfaces\Collection;

interface ResourceCollection extends ResourceBasic
{
    /**
     * @param int $collection_offset
     */
    function setOffset($collection_offset);

    /**
     * @return int
     */
    function getOffset();

    /**
     * @param Collection $ItemCollection
     */
    function setItemCollection($ItemCollection);

    /**
     * @return Collection
     */
    function getItemCollection();

    /**
     * @param int $collection_limit
     */
    function setLimit($collection_limit);

    /**
     * @return int
     */
    function getLimit();
}