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

interface Paginator
{
    /**
     * @return int
     */
    function getCurrentPage();

    /**
     * @return int
     */
    function getTotal();

    function getPageCount();

    /**
     * @param int $items_per_page
     */
    function setItemsPerPage($items_per_page);

    /**
     * @return int
     */
    function getItemsPerPage();

    /**
     * @param int $total
     */
    function setTotal($total);

    /**
     * @param int $current_page
     */
    function setCurrentPage($current_page);
}