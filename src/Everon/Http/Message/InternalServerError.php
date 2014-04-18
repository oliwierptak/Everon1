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

class InternalServerError extends AbstractMessage 
{
    protected $http_status = 500;
    protected $http_message = 'Internal Server Error';
}