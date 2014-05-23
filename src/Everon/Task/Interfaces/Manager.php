<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Task\Interfaces;

use Everon\Domain;

interface Manager
{
    /**
     * @param array $tasks
     * @return mixed
     */
    function process(array $tasks);

    /**
     * @param Item $Item
     */
    function processOne(Item $Item);
}