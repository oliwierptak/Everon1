<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest;

use Everon\Helper;


class Resource implements Interfaces\Resource
{
    use Helper\ToArray;
    
    protected $name = null;
    
    protected $href = null;

    
    public function __construct($name, array $data)
    {
        $this->name = $name;
        $this->data = $data;
    }
}
