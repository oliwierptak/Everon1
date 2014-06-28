<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Resource;

use Everon\Helper;
use Everon\Interfaces\Collection as ItemCollection;
use Everon\Interfaces\Paginator as Paginator;
use Everon\Rest\Interfaces;

class Collection extends Basic implements Interfaces\ResourceCollection
{
    use Helper\Arrays;
    
    protected $limit = null;
    protected $offset = null;
    protected $first = null;
    protected $prev = null;
    protected $next = null;
    protected $last = null;
    
    /**
     * @var ItemCollection
     */
    protected $ItemCollection = null;

    /**
     * @var Paginator
     */
    protected $Paginator = null;

    /**
     * @param Interfaces\ResourceHref $Href
     * @param ItemCollection $ItemCollection
     * @param Paginator $Paginator
     */
    public function __construct(Interfaces\ResourceHref $Href, ItemCollection $ItemCollection, Paginator $Paginator)
    {
        parent::__construct($Href);
        $this->ItemCollection = $ItemCollection;
        $this->Paginator = $Paginator;
    }

    protected function getToArray()
    {
        $data = parent::getToArray();
        //$data = $this->arrayMergeDefault($data, $this->getPaginator()->toArray());
        
        $data['first'] = $this->first;
        $data['prev'] = $this->prev;
        $data['next'] = $this->next;
        $data['last'] = $this->last;
        $data['limit'] = $this->getPaginator()->getLimit();
        $data['offset'] = $this->getPaginator()->getOffset();
        $data['total'] = $this->getPaginator()->getTotal();
        $data['items'] = $this->ItemCollection->toArray(true);
        
        return $data;
    }

    /**
     * @param \Everon\Interfaces\Paginator $Paginator
     */
    public function setPaginator(Paginator $Paginator)
    {
        $this->Paginator = $Paginator;
    }

    /**
     * @return \Everon\Interfaces\Paginator
     */
    public function getPaginator()
    {
        return $this->Paginator;
    }
    
    /**
     * @inheritdoc
     */
    public function setOffset($collection_offset)
    {
        $this->offset = $collection_offset;
    }

    /**
     * @inheritdoc
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @inheritdoc
     */
    public function setItemCollection($ItemCollection)
    {
        $this->ItemCollection = $ItemCollection;
    }

    /**
     * @inheritdoc
     */
    public function getItemCollection()
    {
        return $this->ItemCollection;
    }

    /**
     * @inheritdoc
     */
    public function setLimit($collection_limit)
    {
        $this->limit = $collection_limit;
    }

    /**
     * @inheritdoc
     */
    public function getLimit()
    {
        return $this->limit;
    }
}