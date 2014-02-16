<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Module\Interfaces;

use Everon\Interfaces\Dependency;

interface Manager extends Dependency\ConfigManager
{
    /**
     * @return \Everon\Interfaces\Module
     */
    public function getCoreModule();
    
    /**
     * @return \Everon\Interfaces\Module
     */
    function getDefaultModule();

    /**
     * @param $name
     * @return \Everon\Interfaces\Module
     */
    function getModule($name);

    /**
     * @return array
     */
    function getPathsOfActiveModules();
}
