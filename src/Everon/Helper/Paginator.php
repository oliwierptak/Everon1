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
    protected $current_page = null;

    /**
     * @var int
     */
    protected $offset = null;

    /**
     * @var int
     */
    protected $limit = null;
    
    
    public function __construct($total, $offset, $limit)
    {
        $this->total = (int) $total;
        $this->offset = (int) $offset;
        $this->limit = (int) $limit;
    }

    /**
     * @inheritdoc
     */
    public function setCurrentPage($current_page)
    {
        $page_count = $this->getPageCount();
        $current_page = (int) $current_page;
        $current_page = ($current_page > $page_count) ? $page_count : $current_page;
        $current_page = ($current_page <= 0) ? 0 : $current_page - 1;
        $offset = (int) ($current_page) * $this->getLimit();
        
        $this->current_page = $current_page + 1;
        
        $this->setOffset($offset);
    }

    /**
     * @inheritdoc
     */
    public function getCurrentPage()
    {
        $this->calculateCurrentPage();
        return $this->current_page;
    }

    /**
     * @inheritdoc
     */
    public function setTotal($total)
    {
        $this->total = (int) $total;
    }

    /**
     * @inheritdoc
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @inheritdoc
     */
    public function getPageCount()
    {
        return (int) ceil($this->getTotal() / $this->getLimit());
    }

    /**
     * @inheritdoc
     */
    public function setLimit($limit)
    {
        $this->limit = (int) $limit;
    }

    /**
     * @inheritdoc
     */
    public function getLimit()
    {
        if ((int) $this->limit <= 0) {
            $this->limit = 10;
        }
        return $this->limit;
    }

    /**
     * @inheritdoc
     */
    public function setOffset($offset)
    {
        $max = $this->getTotal() - $this->getOffset();
        $max = ($max < 0) ? 0 : $max;
        $max = ($max >= $this->getTotal()) ? $this->getTotal() - $this->getLimit() : $max; //set to last page if offset is too big
        
        $offset = ($offset < 0) ? 0 : $offset;
        $offset = ($offset > $max) ? $max : $offset;
        
        $this->offset = (int) $offset;
    }

    /**
     * @inheritdoc
     */
    public function getOffset()
    {
        return $this->offset;
    }

    protected function calculateCurrentPage()
    {
        $this->current_page = (int) floor($this->getOffset() / $this->getLimit()) + 1;
    }
}
