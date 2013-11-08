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

interface ConfigExpressionMatcher
{
    /**
     * @param array $configs_data
     * @param array $custom_expressions
     * @return callable
     */
    function createCompiler(array $configs_data, array $custom_expressions=[]);
}