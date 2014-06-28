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
        
        $this->calculateCurrentPage();
    }

    /**
     * @inheritdoc
     */
    public function setCurrentPage($current_page)
    {
        $this->current_page = $current_page;
        $offset = $current_page * $this->getLimit();
        $this->setOffset($offset);
    }

    /**
     * @inheritdoc
     */
    public function getCurrentPage()
    {
        return $this->current_page;
    }

    /**
     * @inheritdoc
     */
    public function setTotal($total)
    {
        $this->total = $total;
        $this->calculateCurrentPage();
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
        return ceil($this->getTotal() / $this->getLimit());
    }

    /**
     * @inheritdoc
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        $this->calculateCurrentPage();
    }

    /**
     * @inheritdoc
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @inheritdoc
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        $this->calculateCurrentPage();
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
        $this->current_page = ceil($this->offset / $this->limit) + 1;
    }
}   