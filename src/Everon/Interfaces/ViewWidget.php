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

interface ViewWidget extends \Everon\Interfaces\Dependency\ViewManager
{
    /**
     * @param \Everon\Interfaces\View $View
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
     * @param mixed $data
     */
    function setData($data);

    /**
     * @return mixed
     */
    function getData();

    /**
     * @return \Everon\Interfaces\View
     */
    function getView();

    /**
     * @return string
     */
    function render();
}