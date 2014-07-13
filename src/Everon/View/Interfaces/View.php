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


interface View extends \Everon\Interfaces\Dependency\GetUrl, \Everon\View\Interfaces\Dependency\WidgetManager
{
    /**
     * @inheritdoc
     */
    function getName();

    /**
     * @param $value
     */
    function setContainerFromString($value);

    /**
     * @inheritdoc
     */
    function setTemplateDirectory($directory);

    /**
     * @return array
     */
    function getData();

    /**
     * @return string
     */
    function getDefaultExtension();

    /**
     * @param $extension
     */
    function setDefaultExtension($extension);

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    function get($name, $default = null);

    /**
     * @param $action
     * @return null
     */
    function execute($action);

    /**
     * @param $name
     */
    function delete($name);

    /**
     * @param $name
     */
    function setName($name);

    /**
     * @param array $data
     */
    function setData(array $data);

    /**
     * @param $name
     * @param $data
     * @return Template|null
     */
    function getTemplate($name, $data);

    /**
     * @return \SplFileInfo
     */
    function getFilename();

    /**
     * @inheritdoc
     */
    function getTemplateDirectory();

    /**
     * @param TemplateContainer $Container
     */
    function setContainer(TemplateContainer $Container);

    /**
     * @param $name
     * @param $value
     */
    function set($name, $value);

    /**
     * @return Template|TemplateContainer
     * @throws \Everon\Exception\View
     */
    function getContainer();

    /**
     * @param array $data
     * @return \Everon\Helper\PopoProps
     */
    function templetize(array $data);

    /**
     * @param array $data Array of items implementing Arrayable Interface
     * @return array Array of Helper\PopoProps objects
     */
    function templetizeArrayable(array $data);
}