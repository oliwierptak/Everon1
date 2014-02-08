<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http\Exception;

use Everon\Interfaces;

class NotFound extends \Everon\Http\Exception 
{
    protected $http_status = 404;
    protected $http_message = 'Not found';
}