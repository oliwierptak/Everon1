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

/**
 * @method \Everon\Interfaces\Router getRouter
 * @requires \Everon\Interfaces\Router
 */
trait GetUrl
{
    /**
     * @inheritdoc
     */
    public function getUrl($name, $query=[], $get=[])
    {
        return $this->getRouter()->getUrl($name, $query, $get);
    }
}