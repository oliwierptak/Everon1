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
    
    const DEFAULT_LIMIT = 10;
    const DEFAULT_OFFSET = 0;

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
        $this->setTotal($total);
        
        //order of execution matters, limit before offset
        $this->setLimit($limit);
        $this->setOffset($offset);
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
        if ((int) $limit <= 0) {
            $limit = static::DEFAULT_LIMIT;
        }

        if ((int) $limit > $this->getTotal()) {
            $limit = $this->getTotal();
        }

        if ((int) $limit <= 0) {
            $limit = static::DEFAULT_LIMIT;
        }

        $this->limit = (int) $limit;
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
        $max = $this->getTotal() - $this->getOffset();
        
        if ($offset > $max || $max <= 0) {
            $offset = $this->getTotal() - $this->getLimit();
        }
        
        if ((int) $offset < 0) {
            $offset = static::DEFAULT_OFFSET;
        }

        if ((int) $offset > $this->getTotal()) {
            $offset = static::DEFAULT_OFFSET;
        }
        
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
