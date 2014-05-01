<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http\Message;

class NotFound extends AbstractMessage 
{
    protected $http_status_code = 404;
    protected $http_message = 'NOT FOUND';
}