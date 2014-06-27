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
use Everon\Rest\Interfaces;
use Everon\Interfaces\Collection as ItemCollection;

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
     * @var Interfaces\Collection
     */
    protected $ItemCollection = null;

    /**
     * @param Interfaces\ResourceHref $Href
     * @param ItemCollection $ItemCollection
     */
    public function __construct(Interfaces\ResourceHref $Href, ItemCollection $ItemCollection, $Paginator)
    {
        parent::__construct($Href);
        $this->ItemCollection = $ItemCollection;
    }

    protected function getToArray()
    {
        $data = parent::getToArray();
        $data = $this->arrayMergeDefault($data, $this->getPaginator()->toArray());
        
        /*
        $data['first'] = $this->first;
        $data['prev'] = $this->prev;
        $data['next'] = $this->next;
        $data['last'] = $this->last;
        $data['limit'] = $this->limit;
        $data['offset'] = $this->offset;
        $data['count'] = $total_count;
        */
        $data['items'] = $this->ItemCollection->toArray(true);
        
        return $data;
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