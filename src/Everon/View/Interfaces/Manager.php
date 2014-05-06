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

use Everon\Exception;
use Everon\Interfaces;

interface Manager
{
    /**
     * @param Template $scope_name
     * @param TemplateContainer $Template
     * @throws \Everon\Exception\ViewManager
     */
    function compileTemplate($scope_name, TemplateContainer $Template);

    /**
     * @param $action
     * @param View $View
     */
    function compileView($action, View $View);

    /**
     * @param $name
     * @param $template_directory
     * @param $namespace
     * @return View
     * @throws \Everon\Exception\ViewManager
     */
    function createView($name, $template_directory, $namespace);

    /**
     * @param $name
     * @param string $namespace
     * @return Widget
     */
    function createWidget($name, $namespace='Everon\View');

    /**
     * @return array
     */
    function getCompilers();

    /**
     * @param string $view_name
     * @return View
     */
    function getCurrentTheme($view_name);

    /**
     * @param $theme_name
     * @param $view_name
     * @return View
     * @throws \Everon\Exception\ViewManager
     */
    function getTheme($theme_name, $view_name);

    /**
     * @param $name
     * @param View $View
     */
    function setTheme($name, View $View);

    /**
     * @param string $theme
     */
    function setCurrentThemeName($theme);
        
    function getCurrentThemeName();

    /**
     * @param string $theme_directory
     */
    function setViewDirectory($theme_directory);

    /**
     * @return string
     */
    function getViewDirectory();

    /**
     * @param string $cache_directory
     */
    function setCacheDirectory($cache_directory);

    /**
     * @return string
     */
    function getCacheDirectory();

    /**
     * @param $name
     */
    function includeWidget($name);

}