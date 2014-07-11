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

trait GetUrl 
{
    /**
     * @inheritdoc
     */
    public function getUrl($name, $query=[], $get=[])
    {
        $Item = $this->getConfigManager()->getConfigByName('router')->getItemByName($name);
        if ($Item === null) {
            throw new \Everon\Exception\Application('Invalid router config name: "%s"', $name);
        }

        return \Everon\Controller::generateUrl($Item, $query, $get);
    }
}