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

interface Manager 
{
    /**
     * @param string $cache_directory
     */
    function setCacheDirectory($cache_directory);

    function getCompilers();

    /**
     * @param $name
     * @param string $namespace
     * @return View
     * @throws \Everon\Exception\ViewManager
     */
    function createLayout($name, $namespace = 'Everon\View');

    /**
     * @return string
     */
    function getViewDirectory();

    /**
     * @param View $View
     */
    function setLayoutByLayoutName(View $View);

    /**
     * @return string
     */
    function getCurrentThemeName();

    /**
     * @inheritdoc
     */
    function setViewDirectory($theme_directory);

    /**
     * @param $view_name
     * @param $template_directory
     * @param string $namespace
     * @return View
     */
    function createView($view_name, $template_directory=null, $namespace='Everon\View');

    /**
     * @inheritdoc
     */
    function getLayoutByName($name);

    /**
     * @return string
     */
    function getCacheDirectory();

    /**
     * @param string $theme
     */
    function setCurrentThemeName($theme);

    /**
     * @inheritdoc
     */
    function compileView($action, View $View);

    /**
     * @param WidgetManager $WidgetManager
     */
    function setWidgetManager(WidgetManager $WidgetManager);

    /**
     * @return WidgetManager
     */
    function getWidgetManager();
}