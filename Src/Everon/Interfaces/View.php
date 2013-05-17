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

interface View
{
    function getOutput();
    function setOutput($Output);
    function getTemplateDirectory();
    function setTemplateDirectory($directory);
    function getTemplateFilename($filename);
    function getTemplate($name, $data);
    function set($name, $data);
    function get($name);

    function setData(array $data);
    function getData();
    function setTemplateFromAction($template, array $data);
}