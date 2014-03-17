<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain\Entity;

/**
 * @method array getId()
 */
abstract class Composite extends \Everon\Domain\Entity
{

    protected $id_name = [];

    protected function isIdSet()
    {
        return count($this->getId()) > 0;
    }

}
