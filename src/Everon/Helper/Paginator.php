<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper;

use Everon\Helper;
use Everon\Interfaces;


class Paginator implements Interfaces\Arrayable, \Everon\Interfaces\Paginator
{
    use Helper\ToArray;

    /**
     * @var int
     */
    protected $total = null;

    /**
     * @var int
     */
    protected $items_per_page = null;

    /**
     * @var int
     */
    protected $current_page = null;
    
    
    public function __construct($total_count, $items_per_page=10)
    {
        $this->total = $total_count;
        $this->items_per_page = $items_per_page;
    }

    /**
     * @param int $current_page
     */
    public function setCurrentPage($current_page)
    {
        $this->current_page = $current_page;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->current_page;
    }

    /**
     * @param int $items_per_page
     */
    public function setItemsPerPage($items_per_page)
    {
        $this->items_per_page = $items_per_page;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->items_per_page;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
    
    public function getPageCount()
    {
        $page_count = ceil($this->getTotal() / $this->getItemsPerPage());
        return $page_count;
    }
    
}