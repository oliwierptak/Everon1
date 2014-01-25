<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain;

class Manager extends Handler implements Interfaces\Manager
{
    /**
     * @inheritdoc
     */
    public function getUserRepository()
    {
        return $this->getRepository('User');
    }
}
