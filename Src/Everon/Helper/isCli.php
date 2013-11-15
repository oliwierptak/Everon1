<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper;

use Everon\Exception;

trait IsCli
{
    protected function isCli()
    {
        return (php_sapi_name() === 'cli');
    }
}