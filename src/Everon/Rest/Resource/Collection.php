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

use Everon\Rest\Resource;
use Everon\Interfaces;

class Collection extends Resource
{
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

    public function __construct($name, $version, Interfaces\Collection $ItemCollection)
    {
        parent::__construct($name, $version, []);
        $this->ItemCollection = $ItemCollection;
    }

    protected function getToArray()
    {
        $data = parent::getToArray();
        $data['first'] = $this->first;
        $data['prev'] = $this->prev;
        $data['next'] = $this->next;
        $data['last'] = $this->last;
        $data['limit'] = $this->limit;
        $data['offset'] = $this->offset;
        
        $items = [];
        foreach ($this->ItemCollection as $Item) {
            $items[] = $Item->toArray();
        }

        $data['items'] = $items;
        
        return $data;
    }

    /**
     * @param null $collection_offset
     */
    public function setOffset($collection_offset)
    {
        $this->offset = $collection_offset;
    }

    /**
     * @return null
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param \Everon\Interfaces\Collection $ItemCollection
     */
    public function setItemCollection($ItemCollection)
    {
        $this->ItemCollection = $ItemCollection;
    }

    /**
     * @return \Everon\Interfaces\Collection
     */
    public function getItemCollection()
    {
        return $this->ItemCollection;
    }

    /**
     * @param null $collection_limit
     */
    public function setLimit($collection_limit)
    {
        $this->limit = $collection_limit;
    }

    /**
     * @return null
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    
    
}
