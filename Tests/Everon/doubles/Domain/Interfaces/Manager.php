<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Domain\Interfaces;

interface Manager extends \Everon\Domain\Interfaces\Manager
{
    /**s
     * @return \Everon\Domain\User\Repository;
     */
    function getUserRepository();
}