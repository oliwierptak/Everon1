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

abstract class Collection extends Resource
{
    protected $collection_limit = null;
    protected $collection_offset = null;
    protected $collection_items = null;

    public function __construct($name, $version, array $items=[])
    {
        parent::__construct($name, $version);
        $this->collection_items = $items;
    }
    
}
