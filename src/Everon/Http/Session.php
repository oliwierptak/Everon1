<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http;

use Everon\Helper;
use Everon\Interfaces\Collection;

class Session implements Interfaces\Session
{
    /**
     * @var Collection
     */
    protected $Data = null;
    
    /**
     * @param $evrid
     * @param array $data
     */
    public function __construct($evrid, array $data)
    {
        $data['evrid'] = $evrid;
        $data['start_time'] = time();
        $this->Data = new Helper\Collection($data);
    }
}