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

use Everon\Config\Interfaces\ItemRouter;

interface RequestValidator
{
    function validate(ItemRouter $RouteItem, Request $Request);
    
    /**
     * @param array $validation_errors
     */
    function setErrors($validation_errors);

    /**
     * @return array
     */
    function getErrors();

    /**
     * @return bool
     */
    function isValid();

    /**
     * @param $name
     * @param $message
     */
    function addError($name, $message);
    
    /**
     * @param $name
     */
    public function removeError($name);
    
}