<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

use Everon\Interfaces;

//todo: replace this and router interfaces with general config interfaces
interface ConfigView
{
    function setDefaultPage(Interfaces\ConfigItemView $ViewItem);
    function getDefaultPage();

    /**
     * @return \array array of \Everon\ConfigItemView objects
     */
    function getPages();

    /**
     * @param string $page_name
     * @return \Everon\Config\Item\View
     */
    function getPageByName($page_name);
}