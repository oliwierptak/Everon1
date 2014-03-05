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
    protected $collection_limit = null;

    protected $collection_offset = null;

    /**
     * @var Interfaces\Collection
     */
    protected $ItemCollection = null;

    public function __construct($name, $version, Interfaces\Collection $ItemCollection)
    {
        parent::__construct($name, $version);
        $this->ItemCollection = $ItemCollection;
    }

    protected function init()
    {
        $this->data['limit'] = $this->collection_limit;
        $this->data['offset'] = $this->collection_offset;
        $this->data['items'] = $this->ItemCollection->toArray(true);
    }

    /**
     * @param null $collection_offset
     */
    public function setOffset($collection_offset)
    {
        $this->collection_offset = $collection_offset;
    }

    /**
     * @return null
     */
    public function getOffset()
    {
        return $this->collection_offset;
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
        $this->collection_limit = $collection_limit;
    }

    /**
     * @return null
     */
    public function getLimit()
    {
        return $this->collection_limit;
    }
    
}
