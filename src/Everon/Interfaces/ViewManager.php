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

use Everon\Exception;
use Everon\Interfaces;

interface ViewManager
{
    /**
     * @param Interfaces\Template $scope_name
     * @param Interfaces\Template $Template
     * @throws \Everon\Exception\ViewManager
     */
    function compileTemplate($scope_name, Interfaces\Template $Template);

    /**
     * @param $action
     * @param Interfaces\View $View
     */
    function compileView($action, Interfaces\View $View);

    /**
     * @param $name
     * @param $template_directory
     * @param $namespace
     * @return Interfaces\View
     * @throws \Everon\Exception\ViewManager
     */
    function createView($name, $template_directory, $namespace);    

    /**
     * @return array
     */
    function getCompilers();

    /**
     * @return Interfaces\View
     */
    function getDefaultTheme();

    /**
     * @param $name
     * @return mixed
     * @throws \Everon\Exception\View
     * @throws \Everon\Exception\ViewManager
     */
    function getTheme($name);

    /**
     * @param $name
     * @param Interfaces\View $View
     */
    function setTheme($name, Interfaces\View $View);

    /**
     * @param string $theme
     */
    function setThemeName($theme);
        
    function getThemeName();
}