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
use Everon\Http\Exception;

class CookieCollection extends Helper\Collection implements Interfaces\CookieCollection
{
    public function __construct(array $data)
    {
        foreach ($data as $Item) {
            if (($Item instanceof Interfaces\Cookie) === false) {
                throw new Exception\CookieCollection('Only Cookies allowed in Collection');
            }
        }
     
        parent::__construct($data);
    }
}