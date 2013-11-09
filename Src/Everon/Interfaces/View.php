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

interface View
{
    function getContainer();
    function setContainer($Container);
    function getTemplateDirectory();
    function setTemplateDirectory($directory);
    function getTemplateFilename($filename);
    function getTemplate($name, $data);
    function set($name, $value);
    function get($name);
    function getData();
    function setData(array $data);
    function getViewTemplateByAction($action);
    function url($url);
    function setDefaultExtension($extension);
    function getDefaultExtension();
    function getViewTemplate();
}