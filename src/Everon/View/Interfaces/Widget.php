<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Interfaces;


interface Widget extends Dependency\Manager
{
    /**
     * @param \Everon\View\Interfaces\View $View
     */
    function setView($View);

    /**
     * @return mixed
     */
    function getName();

    /**
     * @param mixed $name
     */
    function setName($name);

    /**
     * @return \Everon\View\Interfaces\View
     */
    function getView();

    /**
     * @return string
     */
    function render();

    /**
     * @param boolean $has_data
     */
    function setHasData($has_data);

    /**
     * @return boolean
     */
    function hasData();
}